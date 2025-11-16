<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\CashTransactionRequest;
use App\Services\TransactionService;
use App\Models\CashTransaction;
use App\Models\Account;
use App\Models\CashflowCategory;

class CashTransactionController extends Controller
{
    protected $transactionService;
    
    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }
    
    public function index()
    {
        $transactions = CashTransaction::with(['cashAccount', 'contraAccount', 'cashflowCategory'])
            ->orderBy('date', 'desc')
            ->paginate(20);
            
        return view('transactions.cash.index', compact('transactions'));
    }
    
    public function create()
    {
        $cashAccounts = Account::where('type', 'asset')
            ->where('name', 'like', '%kas%')
            ->get();
        $accounts = Account::where('is_active', true)->get();
        $cashflowCategories = CashflowCategory::where('is_active', true)->get();
        
        return view('transactions.cash.create', compact('cashAccounts', 'accounts', 'cashflowCategories'));
    }
    
    public function store(CashTransactionRequest $request)
    {
        try {
            $transaction = $this->transactionService->createCashTransaction($request->validated());
            return redirect()->route('cash-transactions.index')
                ->with('success', 'Transaksi kas berhasil disimpan');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }
    
    public function edit(CashTransaction $cashTransaction)
    {
        $cashAccounts = Account::where('type', 'asset')
            ->where('name', 'like', '%kas%')
            ->get();
        $accounts = Account::where('is_active', true)->get();
        $cashflowCategories = CashflowCategory::where('is_active', true)->get();
        
        return view('transactions.cash.edit', compact('cashTransaction', 'cashAccounts', 'accounts', 'cashflowCategories'));
    }
    
    public function update(CashTransactionRequest $request, CashTransaction $cashTransaction)
    {
        try {
            $this->transactionService->updateCashTransaction($cashTransaction, $request->validated());
            return redirect()->route('cash-transactions.index')
                ->with('success', 'Transaksi kas berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }
    
    public function destroy(CashTransaction $cashTransaction)
    {
        $cashTransaction->delete();
        return redirect()->route('cash-transactions.index')
            ->with('success', 'Transaksi kas berhasil dihapus');
    }
}