<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Account;
use App\Models\Ledger;
use App\Models\Cashflow;
use App\Models\TrialBalance;
use App\Services\AssetFromTransactionService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashBankJournalController extends Controller
{
    protected $assetFromTransactionService;

    public function __construct(AssetFromTransactionService $assetFromTransactionService)
    {
        $this->assetFromTransactionService = $assetFromTransactionService;
    }
    public function create(Request $request)
    {
        $selectedLedger = null;
        $selectedAccount = null;
        $openingBalance = 0;
        $journalsHistory = [];
        $year = $request->get('year', date('Y'));

        if ($request->filled('ledger_id')) {

            // Pakai eager loading trialBalance
            $selectedLedger = Ledger::with('trialBalance')->find($request->ledger_id);

            if ($selectedLedger && $selectedLedger->trialBalance) {

                $selectedAccount = $selectedLedger->trialBalance;

                // Hitung opening balance
                $openingBalance = $this->calculateOpeningBalance($selectedAccount->id);

                // Ambil history jurnal dengan filter tahun
                $journalsHistory = $this->getJournalsHistory($selectedLedger->id, $year);
            }
        }

        $accounts = TrialBalance::orderBy('kode')->where('level', 4)->get();
        $cashflows = Cashflow::orderBy('kode')->where('level', 3)->get();

        return view('journals.create', compact(
            'selectedLedger',
            'selectedAccount',
            'openingBalance',
            'journalsHistory',
            'accounts',
            'cashflows'
        ));
    }

    public function store(Request $request)
    {
        // rules dasar (files di-handle sebagai entries.*.attachments.*)
        $rules = [
            'selected_cash_account_id' => 'required|exists:trial_balances,id',
            'entries' => 'required|array|min:1',

            'entries.*.date' => 'nullable|date',
            'entries.*.description' => 'nullable|string|max:255',
            'entries.*.pic' => 'nullable|string|max:255',
            'entries.*.proof_number' => 'nullable|string|max:255',

            'entries.*.cash_in' => 'nullable|numeric|min:0',
            'entries.*.cash_out' => 'nullable|numeric|min:0',

            'entries.*.debit_account_id' => 'nullable|exists:trial_balances,id',
            'entries.*.credit_account_id' => 'nullable|exists:trial_balances,id',
            'entries.*.cashflow_id' => 'nullable|exists:cashflows,id',

            // file rule untuk attachments array
            'entries.*.attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        $messages = [
            'entries.required' => 'Minimal 1 entri harus diisi.',
            'entries.*.cash_in.numeric' => 'Cash in harus berupa angka.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // tambahan logika
        $validator->after(function ($v) use ($request) {
            $entries = $request->input('entries', []);
            foreach ($entries as $index => $entry) {
                $hasCash = (isset($entry['cash_in']) && $entry['cash_in'] !== '') ||
                    (isset($entry['cash_out']) && $entry['cash_out'] !== '');

                if ($hasCash) {
                    if (empty($entry['description'])) {
                        $v->errors()->add("entries.$index.description", 'Deskripsi wajib jika ada nilai cash in atau cash out.');
                    }
                    if (empty($entry['date'])) {
                        $v->errors()->add("entries.$index.date", 'Tanggal wajib jika ada nilai cash in atau cash out.');
                    }
                }

                if (($entry['cash_in'] ?? 0) > 0 && empty($entry['credit_account_id'])) {
                    $v->errors()->add("entries.$index.credit_account_id", 'Akun kredit wajib untuk cash in.');
                }
                if (($entry['cash_out'] ?? 0) > 0 && empty($entry['debit_account_id'])) {
                    $v->errors()->add("entries.$index.debit_account_id", 'Akun debit wajib untuk cash out.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validated = $validator->validated();

        DB::transaction(function () use ($validated, $request) {
            $cashAccountId = $validated['selected_cash_account_id'];
            $currentBalance = $this->calculateCurrentBalance($cashAccountId);

            foreach ($validated['entries'] as $i => $entry) {

                // cek apakah ada file
                $filesGroup = $request->file('entries')[$i]['attachments'] ?? null;
                $hasFile = $filesGroup && is_array($filesGroup) && count($filesGroup) > 0;

                // kalau tidak ada file → proof_number harus null
                if (!$hasFile) {
                    $entry['proof_number'] = null;
                }

                $cashIn = $entry['cash_in'] ?? 0;
                $cashOut = $entry['cash_out'] ?? 0;
                $currentBalance += $cashIn - $cashOut;

                $journal = Journal::create([
                    'date' => $entry['date'],
                    'number' => $this->generateJournalNumber(),
                    'description' => $entry['description'] ?? null,
                    'pic' => $entry['pic'] ?? null,
                    'proof_number' => $entry['proof_number'], // sudah aman
                    'cash_in' => $cashIn,
                    'cash_out' => $cashOut,
                    'debit_account_id' => $entry['debit_account_id'] ?? null,
                    'credit_account_id' => $entry['credit_account_id'] ?? null,
                    'cashflow_id' => $entry['cashflow_id'] ?? null,
                    'balance' => $currentBalance,
                    'total_debit' => max($cashIn, $cashOut),
                    'total_credit' => max($cashIn, $cashOut),
                    'source_module' => 'manual',
                    'is_posted' => true,
                    'created_by' => auth()->id(),
                    'ledger_id' => $request->input('ledger_id'),
                ]);

                // handle attachments
                if ($hasFile) {
                    foreach ($filesGroup as $file) {
                        if (!$file)
                            continue;

                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('journal_attachments', $filename, 'public');

                        $journal->attachments()->create([
                            'original_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_type' => $file->getClientMimeType(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            }
        });

        return redirect()
            ->route('journals.create', ['ledger_id' => $request->input('ledger_id')])
            ->with('success', 'Jurnal berhasil disimpan');
    }


    // private function calculateOpeningBalance($accountId)
    // {
    //     $account = Account::find($accountId);
    //     return $account ? $account->opening_balance : 0;
    // }
    private function calculateOpeningBalance($tbId)
    {
        $trialBalance = TrialBalance::find($tbId);
        return $trialBalance ? $trialBalance->tahun_2024 : 0;
    }

    private function calculateCurrentBalance($accountId)
    {
        $balance = $this->calculateOpeningBalance($accountId);

        $journals = Journal::where(function ($query) use ($accountId) {
            $query->where('debit_account_id', $accountId)
                ->orWhere('credit_account_id', $accountId);
        })->orderBy('date')->get();

        foreach ($journals as $journal) {
            $balance += $journal->cash_in - $journal->cash_out;
        }

        return $balance;
    }

    private function getJournalsHistory($ledgerId, $year = null)
    {
        $query = Journal::with(['debitAccount', 'creditAccount', 'cashflow', 'attachments'])
            ->where('ledger_id', $ledgerId);
            
        if ($year) {
            $query->whereYear('date', $year);
        }
        
        $journals = $query->orderBy('date')
            ->orderBy('created_at')
            ->get();

        $history = [];
        $ledger = Ledger::with('trialBalance')->find($ledgerId);
        $runningBalance = $ledger && $ledger->trialBalance ? $ledger->trialBalance->tahun_2024 : 0;

        foreach ($journals as $journal) {
            $runningBalance += $journal->cash_in - $journal->cash_out;

            $history[] = [
                'journal_id' => $journal->id,
                'date' => $journal->date->format('d/m/Y'),
                'description' => $journal->description,
                'pic' => $journal->pic,
                'proof_number' => $journal->proof_number,
                'cash_in' => $journal->cash_in,
                'cash_out' => $journal->cash_out,
                'debit_account' => $journal->debitAccount ? $journal->debitAccount->kode . ' - ' . $journal->debitAccount->keterangan : '-',
                'credit_account' => $journal->creditAccount ? $journal->creditAccount->kode . ' - ' . $journal->creditAccount->keterangan : '-',
                'cashflow_code' => $journal->cashflow ? $journal->cashflow->kode : null,
                'cashflow_desc' => $journal->cashflow ? $journal->cashflow->keterangan : null,
                'attachments' => $journal->attachments,
                'balance' => $runningBalance,
                'can_create_asset' => $this->assetFromTransactionService->canCreateAssetFromTransaction($journal),
            ];
        }

        return $history;
    }

    private function generateJournalNumber()
    {
        $date = now();
        $prefix = 'JRN-' . $date->format('Ym') . '-';
        $lastJournal = Journal::where('number', 'like', $prefix . '%')
            ->orderBy('number', 'desc')
            ->first();

        if ($lastJournal) {
            $lastNumber = intval(substr($lastJournal->number, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function getAttachments($id)
    {
        $journal = Journal::with('attachments')->findOrFail($id);
        return response()->json(['attachments' => $journal->attachments]);
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'pic' => 'nullable|string|max:255',
            'proof_number' => 'nullable|string|max:255',
            'cash_in' => 'nullable|numeric|min:0',
            'cash_out' => 'nullable|numeric|min:0',
            'cashflow_id' => 'nullable|exists:cashflows,id',
            'debit_account_id' => 'nullable|exists:trial_balances,id',
            'credit_account_id' => 'nullable|exists:trial_balances,id',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            DB::transaction(function () use ($request, $id) {
                $journal = Journal::findOrFail($id);
                
                $journal->update([
                    'date' => $request->date,
                    'description' => $request->description,
                    'pic' => $request->pic,
                    'proof_number' => $request->proof_number,
                    'cash_in' => $request->cash_in ?? 0,
                    'cash_out' => $request->cash_out ?? 0,
                    'cashflow_id' => $request->cashflow_id,
                    'debit_account_id' => $request->debit_account_id,
                    'credit_account_id' => $request->credit_account_id,
                ]);

                // Handle new attachments
                if ($request->hasFile('attachments')) {
                    foreach ($request->file('attachments') as $file) {
                        $filename = time() . '_' . $file->getClientOriginalName();
                        $path = $file->storeAs('journal_attachments', $filename, 'public');

                        $journal->attachments()->create([
                            'original_name' => $file->getClientOriginalName(),
                            'file_path' => $path,
                            'file_type' => $file->getClientMimeType(),
                            'file_size' => $file->getSize(),
                        ]);
                    }
                }
            });

            return response()->json(['success' => true, 'message' => 'Journal updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update journal: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $journal = Journal::findOrFail($id);

            // Delete related attachments first
            if ($journal->attachments) {
                foreach ($journal->attachments as $attachment) {
                    // Delete file from storage
                    if (file_exists(storage_path('app/public/' . $attachment->file_path))) {
                        unlink(storage_path('app/public/' . $attachment->file_path));
                    }
                    $attachment->delete();
                }
            }

            $journal->delete();

            return response()->json(['success' => 'Journal deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete journal: ' . $e->getMessage()], 500);
        }
    }
}