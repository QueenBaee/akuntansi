<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Http\Requests\PostDepreciationRequest;
use App\Services\FixedAssetService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AssetDepreciationController extends Controller
{
    protected $assetService;

    public function __construct(FixedAssetService $assetService)
    {
        $this->assetService = $assetService;
        $this->middleware('auth');
    }

    public function postMemorial(Request $request, $assetId, $period)
    {
        try {
            $asset = FixedAsset::findOrFail($assetId);
            $periodDate = Carbon::parse($period);

            // Validate period format
            if ($periodDate->day !== 1) {
                throw new \Exception('Period harus tanggal 1 dari bulan yang dipilih.');
            }

            // Check if already posted
            if ($this->assetService->isPosted($asset, $periodDate)) {
                throw new \Exception('Penyusutan untuk periode ini sudah diposting.');
            }

            $depreciation = $this->assetService->postDepreciationToMemorial(
                $asset, 
                $periodDate, 
                auth()->id()
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Depreciation posted to memorial successfully',
                    'data' => $depreciation->load('journal')
                ], 201);
            }

            return back()->with('success', 'Penyusutan berhasil diposting ke memorial');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $query = \App\Models\AssetDepreciation::with(['asset', 'journal', 'creator'])
            ->orderBy('period_date', 'desc');

        if ($request->has('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        if ($request->has('year')) {
            $query->whereYear('period_date', $request->year);
        }

        if ($request->has('month')) {
            $query->whereMonth('period_date', $request->month);
        }

        $depreciations = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $depreciations
            ]);
        }

        return view('asset-depreciations.index', compact('depreciations'));
    }
}