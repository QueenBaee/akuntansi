<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\BankTransaction;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    protected $journalService;
    
    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }
    
    public function createCashTransaction(array $data): CashTransaction
    {
        return DB::transaction(function () use ($data) {
            // Create cash transaction record
            $transaction = CashTransaction::create([
                'date' => $data['date'],
                'number' => $this->generateTransactionNumber($data['date'], 'CASH'),
                'type' => $data['type'], // 'in' or 'out'
                'cash_account_id' => $data['cash_account_id'],
                'contra_account_id' => $data['contra_account_id'],
                'cashflow_category_id' => $data['cashflow_category_id'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'reference' => $data['reference'] ?? null,
                'created_by' => auth()->id(),
            ]);
            
            // Create journal entry
            $journalData = [
                'date' => $data['date'],
                'source_module' => 'CASH',
                'reference' => $transaction->id,
                'description' => $data['description'],
                'details' => []
            ];
            
            if ($data['type'] === 'in') {
                // Cash In: Debit Cash, Credit Contra Account
                $journalData['details'] = [
                    [
                        'account_id' => $data['cash_account_id'],
                        'debit' => $data['amount'],
                        'credit' => 0,
                        'description' => $data['description']
                    ],
                    [
                        'account_id' => $data['contra_account_id'],
                        'debit' => 0,
                        'credit' => $data['amount'],
                        'description' => $data['description']
                    ]
                ];
            } else {
                // Cash Out: Debit Contra Account, Credit Cash
                $journalData['details'] = [
                    [
                        'account_id' => $data['contra_account_id'],
                        'debit' => $data['amount'],
                        'credit' => 0,
                        'description' => $data['description']
                    ],
                    [
                        'account_id' => $data['cash_account_id'],
                        'debit' => 0,
                        'credit' => $data['amount'],
                        'description' => $data['description']
                    ]
                ];
            }
            
            $journal = $this->journalService->createJournal($journalData);
            
            // Update transaction with journal reference
            $transaction->update(['journal_id' => $journal->id]);
            
            return $transaction;
        });
    }
    
    public function updateCashTransaction(CashTransaction $transaction, array $data): CashTransaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            // Update transaction record
            $transaction->update([
                'date' => $data['date'],
                'type' => $data['type'],
                'cash_account_id' => $data['cash_account_id'],
                'contra_account_id' => $data['contra_account_id'],
                'cashflow_category_id' => $data['cashflow_category_id'],
                'amount' => $data['amount'],
                'description' => $data['description'],
                'reference' => $data['reference'] ?? null,
            ]);
            
            // Update journal if exists
            if ($transaction->journal) {
                $journalData = [
                    'date' => $data['date'],
                    'description' => $data['description'],
                    'details' => []
                ];
                
                if ($data['type'] === 'in') {
                    $journalData['details'] = [
                        [
                            'account_id' => $data['cash_account_id'],
                            'debit' => $data['amount'],
                            'credit' => 0,
                            'description' => $data['description']
                        ],
                        [
                            'account_id' => $data['contra_account_id'],
                            'debit' => 0,
                            'credit' => $data['amount'],
                            'description' => $data['description']
                        ]
                    ];
                } else {
                    $journalData['details'] = [
                        [
                            'account_id' => $data['contra_account_id'],
                            'debit' => $data['amount'],
                            'credit' => 0,
                            'description' => $data['description']
                        ],
                        [
                            'account_id' => $data['cash_account_id'],
                            'debit' => 0,
                            'credit' => $data['amount'],
                            'description' => $data['description']
                        ]
                    ];
                }
                
                $this->journalService->updateJournal($transaction->journal, $journalData);
            }
            
            return $transaction;
        });
    }
    
    private function generateTransactionNumber(string $date, string $type): string
    {
        $date = \Carbon\Carbon::parse($date);
        $prefix = $type . '/' . $date->format('Ymd') . '/';
        
        $lastNumber = CashTransaction::where('number', 'like', $prefix . '%')
            ->whereDate('date', $date->toDateString())
            ->count();
            
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}