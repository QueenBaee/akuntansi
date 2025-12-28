<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\JournalRequest;
use App\Services\JournalService;
use App\Services\JournalNumberService;
use App\Models\Journal;
use App\Models\Account;
use App\Models\Cashflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    protected $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index()
    {
        $journals = Journal::with('details.account')
            ->orderBy('date', 'desc')
            ->paginate(20);
        $accounts = Account::where('is_active', true)->orderBy('code')->get();

        return view('journals.index', compact('journals', 'accounts'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        
        // Get accounts based on user access
        if ($user->hasRole('admin')) {
            $accounts = Account::where('is_active', true)->orderBy('code')->get();
        } else {
            // Non-admin users only see accounts from their accessible ledgers
            $ledgerIds = $user->activeLedgers()->pluck('ledgers.id');
            $accounts = Account::where('is_active', true)
                ->whereIn('id', $ledgerIds)
                ->orderBy('code')
                ->get();
        }
        
        $cashflows = Cashflow::all();
        
        // Get ledger_id from request parameter
        $selectedLedgerId = $request->get('ledger_id');
        $selectedAccount = null;
        $openingBalance = 0;
        $journalsHistory = collect();
        
        if ($selectedLedgerId) {
            // Check if user has access to this ledger
            if (!$user->hasLedgerAccess($selectedLedgerId)) {
                abort(403, 'You do not have access to this ledger.');
            }
            
            $selectedAccount = Account::find($selectedLedgerId);
            
            if ($selectedAccount) {
                $openingBalance = $selectedAccount->opening_balance ?? 0;
                
                // Get journal history for this account
                $journals = Journal::with(['details.account', 'cashflow', 'debitAccount', 'creditAccount', 'attachments'])
                    ->where(function($q) use ($selectedLedgerId) {
                        $q->where('debit_account_id', $selectedLedgerId)
                          ->orWhere('credit_account_id', $selectedLedgerId)
                          ->orWhereHas('details', function($subQ) use ($selectedLedgerId) {
                              $subQ->where('account_id', $selectedLedgerId);
                          });
                    })
                    ->orderBy('date', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                $runningBalance = $openingBalance;
                $journalsHistory = $journals->map(function($journal) use ($selectedLedgerId, &$runningBalance, $selectedAccount) {
                    // Handle both old format (using debit_account_id/credit_account_id) and new format (using details)
                    $cashIn = 0;
                    $cashOut = 0;
                    $debitAccountName = '';
                    $creditAccountName = '';
                    
                    if ($journal->details->count() > 0) {
                        // New format with journal details
                        $cashDetail = $journal->details->where('account_id', $selectedLedgerId)->first();
                        $cashIn = $cashDetail ? $cashDetail->debit : 0;
                        $cashOut = $cashDetail ? $cashDetail->credit : 0;
                        
                        $contraDetail = $journal->details->where('account_id', '!=', $selectedLedgerId)->first();
                        $debitAccountName = $cashIn > 0 ? ($selectedAccount->code . ' - ' . $selectedAccount->name) : ($contraDetail ? $contraDetail->account->code . ' - ' . $contraDetail->account->name : '');
                        $creditAccountName = $cashOut > 0 ? ($selectedAccount->code . ' - ' . $selectedAccount->name) : ($contraDetail ? $contraDetail->account->code . ' - ' . $contraDetail->account->name : '');
                    } else {
                        // Old format using direct fields
                        $cashIn = ($journal->debit_account_id == $selectedLedgerId) ? $journal->cash_in : 0;
                        $cashOut = ($journal->credit_account_id == $selectedLedgerId) ? $journal->cash_out : 0;
                        
                        $debitAccountName = $journal->debitAccount ? ($journal->debitAccount->code . ' - ' . $journal->debitAccount->name) : '';
                        $creditAccountName = $journal->creditAccount ? ($journal->creditAccount->code . ' - ' . $journal->creditAccount->name) : '';
                    }
                    
                    $runningBalance += $cashIn - $cashOut;
                    
                    return [
                        'journal_id' => $journal->id,
                        'date' => $journal->date->format('Y-m-d'),
                        'description' => $journal->description,
                        'pic' => $journal->pic,
                        'proof_number' => $journal->reference ?? $journal->number,
                        'cash_in' => $cashIn,
                        'cash_out' => $cashOut,
                        'cashflow_code' => $journal->cashflow?->kode,
                        'cashflow_desc' => $journal->cashflow?->keterangan,
                        'debit_account' => $debitAccountName,
                        'credit_account' => $creditAccountName,
                        'attachments' => $journal->attachments,
                        'balance' => $runningBalance
                    ];
                });
            }
        }
        
        return view('journals.create', compact('accounts', 'cashflows', 'selectedAccount', 'openingBalance', 'journalsHistory'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'selected_cash_account_id' => 'required|exists:accounts,id',
            'entries' => 'required|array|min:1',
            'entries.*.date' => 'nullable|date|required_with:entries.*.description,entries.*.cash_in,entries.*.cash_out',
            'entries.*.description' => 'nullable|string|max:255|required_with:entries.*.cash_in,entries.*.cash_out',
            'entries.*.cash_in' => 'nullable|numeric|min:0',
            'entries.*.cash_out' => 'nullable|numeric|min:0',
            'entries.*.debit_account_id' => 'nullable|exists:accounts,id',
            'entries.*.credit_account_id' => 'nullable|exists:accounts,id',
            'entries.*.cashflow_id' => 'nullable|exists:cashflows,id',
            'entries.*.file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);
        
        try {
            $cashAccountId = $request->selected_cash_account_id;
            
            // Check if user has access to this ledger
            $user = auth()->user();
            if (!$user->hasLedgerAccess($cashAccountId)) {
                abort(403, 'You do not have access to this ledger.');
            }
            
            // Get opening balance for validation
            $account = Account::find($cashAccountId);
            $openingBalance = $account->opening_balance ?? 0;
            
            // Calculate current balance from existing journals
            $currentBalance = $openingBalance;
            $existingJournals = Journal::with('details')
                ->whereHas('details', function($q) use ($cashAccountId) {
                    $q->where('account_id', $cashAccountId);
                })
                ->orderBy('date', 'asc')
                ->get();
                
            foreach ($existingJournals as $journal) {
                $cashDetail = $journal->details->where('account_id', $cashAccountId)->first();
                if ($cashDetail) {
                    $currentBalance += $cashDetail->debit - $cashDetail->credit;
                }
            }

            foreach ($request->entries as $index => $entry) {
                if (empty($entry['description']) || (empty($entry['cash_in']) && empty($entry['cash_out']))) {
                    continue;
                }

                $cashIn = floatval($entry['cash_in'] ?? 0);
                $cashOut = floatval($entry['cash_out'] ?? 0);
                $amount = $cashIn > 0 ? $cashIn : $cashOut;
                
                // Handle file upload
                $filePath = null;
                if (isset($entry['file']) && $entry['file']) {
                    $filePath = $entry['file']->store('journal_proofs', 'local');
                }

                // Create journal
                $journal = Journal::create([
                    'date' => $entry['date'],
                    'number' => JournalNumberService::generate($entry['date']),
                    'reference' => $entry['proof_number'] ?? null,
                    'description' => $entry['description'],
                    'source_module' => 'cash',
                    'total_debit' => $amount,
                    'total_credit' => $amount,
                    'is_posted' => true,
                    'created_by' => auth()->id(),
                    'file_path' => $filePath,
                ]);

                // Create journal details
                if ($cashIn > 0) {
                    $journal->details()->create([
                        'account_id' => $cashAccountId,
                        'debit' => $cashIn,
                        'credit' => 0,
                        'description' => $entry['description']
                    ]);

                    $creditAccountId = $entry['credit_account_id'] ?? $cashAccountId;
                    $journal->details()->create([
                        'account_id' => $creditAccountId,
                        'debit' => 0,
                        'credit' => $cashIn,
                        'description' => $entry['description']
                    ]);
                }

                if ($cashOut > 0) {
                    $journal->details()->create([
                        'account_id' => $cashAccountId,
                        'debit' => 0,
                        'credit' => $cashOut,
                        'description' => $entry['description']
                    ]);

                    $debitAccountId = $entry['debit_account_id'] ?? $cashAccountId;
                    $journal->details()->create([
                        'account_id' => $debitAccountId,
                        'debit' => $cashOut,
                        'credit' => 0,
                        'description' => $entry['description']
                    ]);
                }
            }

            return redirect()->route('journals.create', ['ledger_id' => $cashAccountId])
                ->with('success', 'Jurnal berhasil disimpan');

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Journal $journal)
    {
        $journal->load('details.account');
        return view('journals.show', compact('journal'));
    }

    public function edit(Journal $journal)
    {
        $journal->load('details.account');
        $accounts = Account::where('is_active', true)->orderBy('code')->get();
        $cashflows = Cashflow::all();
        return view('journals.edit', compact('journal', 'accounts', 'cashflows'));
    }

    public function update(JournalRequest $request, Journal $journal)
    {
        try {
            $this->journalService->updateJournal($journal, $request->validated());
            return redirect()->route('journals.index')
                ->with('success', 'Jurnal berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Journal $journal)
    {
        try {
            if ($journal->is_posted) {
                $message = 'Cannot delete posted journal';
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            $journal->delete(); // This will soft delete
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Jurnal berhasil dihapus']);
            }
            
            return redirect()->route('journals.create', ['account_id' => session('selected_cash_account_id')])
                ->with('success', 'Jurnal berhasil dihapus');
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
            }
            
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


}