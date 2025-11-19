<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\JournalRequest;
use App\Services\JournalService;
use App\Models\Journal;
use App\Models\Account;

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
    
    public function create()
    {
        $accounts = Account::where('is_active', true)->orderBy('code')->get();
        return view('journals.create', compact('accounts'));
    }
    
    public function store(JournalRequest $request)
    {
        try {
            $journal = $this->journalService->createJournal($request->validated());
            return redirect()->route('journals.index')
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
        return view('journals.edit', compact('journal', 'accounts'));
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
        $journal->delete();
        return redirect()->route('journals.index')
            ->with('success', 'Jurnal berhasil dihapus');
    }
}