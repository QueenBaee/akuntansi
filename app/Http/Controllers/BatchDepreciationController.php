<?php

namespace App\Http\Controllers;

use App\Services\BatchDepreciationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BatchDepreciationController extends Controller
{
    private BatchDepreciationService $batchService;

    public function __construct(BatchDepreciationService $batchService)
    {
        $this->batchService = $batchService;
        // Remove auth middleware for API routes - handle auth in individual methods if needed
    }

    /**
     * Preview eligible assets for a specific month
     */
    public function preview(Request $request)
    {
        try {
            $request->validate([
                'period_month' => 'required|date_format:Y-m'
            ]);

            $preview = $this->batchService->previewEligibleAssets($request->period_month);

            return response()->json([
                'success' => true,
                'data' => $preview
            ]);
        } catch (\Exception $e) {
            \Log::error('Batch depreciation preview error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process batch depreciation for a specific month
     */
    public function process(Request $request)
    {
        try {
            $request->validate([
                'period_month' => 'required|date_format:Y-m'
            ]);

            $result = $this->batchService->processBatchDepreciation(
                $request->period_month,
                1 // Default user ID for now, can be improved later
            );

            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Batch depreciation process error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
