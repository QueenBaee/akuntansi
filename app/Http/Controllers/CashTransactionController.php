<?php

namespace App\Http\Controllers;

use App\Http\Requests\CashTransactionRequest;
use App\Models\CashTransaction;
use App\Services\TransactionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CashTransactionController extends Controller
{
    private TransactionService $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
        $this->middleware('permission:cash.view')->only(['index', 'show']);
        $this->middleware('permission:cash.create')->only(['store']);
        $this->middleware('permission:cash.update')->only(['update']);
        $this->middleware('permission:cash.delete')->only(['destroy']);
    }

    public function index(Request $request): JsonResponse
    {
        $query = CashTransaction::with([
            'cashAccount',
            'contraAccount', 
            'cashflowCategory',
            'journal',
            'creator'
        ]);

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $transactions = $query->orderBy('date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'message' => 'Cash transactions retrieved successfully',
            'data' => $transactions,
        ]);
    }

    public function store(CashTransactionRequest $request): JsonResponse
    {
        try {
            // Generate transaction number
            $number = $this->generateTransactionNumber($request->type, $request->date);
            
            // Create cash transaction
            $cashTransaction = CashTransaction::create([
                'date' => $request->date,
                'number' => $number,
                'type' => $request->type,
                'cash_account_id' => $request->cash_account_id,
                'contra_account_id' => $request->contra_account_id,
                'cashflow_category_id' => $request->cashflow_category_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'reference' => $request->reference,
                'created_by' => auth()->id(),
            ]);

            // Create journal entry
            $journal = $this->transactionService->createCashJournal([
                'id' => $cashTransaction->id,
                'date' => $request->date,
                'type' => $request->type,
                'cash_account_id' => $request->cash_account_id,
                'contra_account_id' => $request->contra_account_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'reference' => $request->reference,
            ]);

            // Update cash transaction with journal reference
            $cashTransaction->update(['journal_id' => $journal->id]);

            return response()->json([
                'message' => 'Cash transaction created successfully',
                'data' => $cashTransaction->load([
                    'cashAccount',
                    'contraAccount',
                    'cashflowCategory',
                    'journal.details.account'
                ]),
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create cash transaction',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(CashTransaction $cashTransaction): JsonResponse
    {
        return response()->json([
            'message' => 'Cash transaction retrieved successfully',
            'data' => $cashTransaction->load([
                'cashAccount',
                'contraAccount',
                'cashflowCategory',
                'journal.details.account',
                'creator'
            ]),
        ]);
    }

    public function update(CashTransactionRequest $request, CashTransaction $cashTransaction): JsonResponse
    {
        try {
            // Check if transaction has been posted to journal
            if ($cashTransaction->journal_id) {
                return response()->json([
                    'message' => 'Cannot update posted transaction',
                ], 422);
            }

            $cashTransaction->update($request->validated());

            return response()->json([
                'message' => 'Cash transaction updated successfully',
                'data' => $cashTransaction->load([
                    'cashAccount',
                    'contraAccount',
                    'cashflowCategory'
                ]),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to update cash transaction',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(CashTransaction $cashTransaction): JsonResponse
    {
        try {
            // Check if transaction has been posted to journal
            if ($cashTransaction->journal_id) {
                return response()->json([
                    'message' => 'Cannot delete posted transaction',
                ], 422);
            }

            $cashTransaction->delete();

            return response()->json([
                'message' => 'Cash transaction deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete cash transaction',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    private function generateTransactionNumber(string $type, string $date): string
    {
        $prefix = $type === 'in' ? 'KM' : 'KK';
        $yearMonth = date('Ym', strtotime($date));
        
        $lastTransaction = CashTransaction::where('number', 'like', "{$prefix}{$yearMonth}%")
            ->orderBy('number', 'desc')
            ->first();
            
        $sequence = 1;
        if ($lastTransaction) {
            $lastSequence = (int) substr($lastTransaction->number, -4);
            $sequence = $lastSequence + 1;
        }
        
        return $prefix . $yearMonth . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}