<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
        $this->middleware('permission:reports.view');
    }

    public function trialBalance(Request $request): JsonResponse
    {
        $request->validate([
            'as_of_date' => 'required|date',
        ]);

        $asOfDate = Carbon::parse($request->as_of_date);
        $trialBalance = $this->reportService->getTrialBalance($asOfDate);

        return response()->json([
            'message' => 'Trial balance retrieved successfully',
            'data' => $trialBalance,
        ]);
    }

    public function incomeStatement(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        $incomeStatement = $this->reportService->getIncomeStatement($startDate, $endDate);

        return response()->json([
            'message' => 'Income statement retrieved successfully',
            'data' => $incomeStatement,
        ]);
    }

    public function balanceSheet(Request $request): JsonResponse
    {
        $request->validate([
            'as_of_date' => 'required|date',
        ]);

        $asOfDate = Carbon::parse($request->as_of_date);
        $balanceSheet = $this->reportService->getBalanceSheet($asOfDate);

        return response()->json([
            'message' => 'Balance sheet retrieved successfully',
            'data' => $balanceSheet,
        ]);
    }

    public function cashFlow(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        $cashFlow = $this->reportService->getCashFlow($startDate, $endDate);

        return response()->json([
            'message' => 'Cash flow statement retrieved successfully',
            'data' => $cashFlow,
        ]);
    }

    public function generalLedger(Request $request): JsonResponse
    {
        $request->validate([
            'account_id' => 'required|exists:accounts,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        
        $generalLedger = $this->reportService->getGeneralLedger(
            $request->account_id,
            $startDate,
            $endDate
        );

        return response()->json([
            'message' => 'General ledger retrieved successfully',
            'data' => $generalLedger,
        ]);
    }
}