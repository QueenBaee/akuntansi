<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\TrialBalance;
use App\Http\Requests\StoreFixedAssetRequest;
use App\Http\Requests\UpdateFixedAssetRequest;
use App\Services\FixedAssetService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FixedAssetController extends Controller
{
    protected $assetService;

    public function __construct(FixedAssetService $assetService)
    {
        $this->assetService = $assetService;
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = FixedAsset::with(['assetAccount', 'accumulatedAccount', 'expenseAccount', 'creator'])
            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $assets = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $assets
            ]);
        }

        return view('fixed-assets.index', compact('assets'));
    }

    public function create()
    {
        // Get asset accounts level 4 where parent has is_aset = 1
        $assetAccounts = TrialBalance::where('level', 4)
            ->whereHas('parent', function($query) {
                $query->where('is_aset', 1);
            })
            ->orderBy('kode')
            ->get();
            
        // Get all accounts for mapping
        $allAccounts = TrialBalance::orderBy('kode')->get();
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'assetAccounts' => $assetAccounts,
                    'allAccounts' => $allAccounts
                ]
            ]);
        }

        return view('fixed-assets.create', compact('assetAccounts', 'allAccounts'));
    }

    public function store(StoreFixedAssetRequest $request)
    {
        $validated = $request->validated();
        
        // Set default values
        $validated['residual_value'] = 1;
        $validated['is_active'] = $validated['status'] === 'active';
        $validated['created_by'] = auth()->id();
        
        // Convert percentage string to decimal
        if (isset($validated['depreciation_rate'])) {
            $validated['depreciation_rate'] = (float) str_replace('%', '', $validated['depreciation_rate']);
        }
        
        $asset = FixedAsset::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fixed asset created successfully',
                'data' => $asset->load(['assetAccount', 'accumulatedAccount', 'expenseAccount'])
            ], 201);
        }

        return redirect()->route('fixed-assets.show', $asset)
            ->with('success', 'Aset tetap berhasil dibuat');
    }

    public function show(FixedAsset $fixedAsset)
    {
        $fixedAsset->load([
            'assetAccount', 
            'accumulatedAccount', 
            'expenseAccount', 
            'creator',
            'mutations',
            'depreciations.journal'
        ]);

        $depreciationSchedule = $this->assetService->generateDepreciationSchedule($fixedAsset);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $fixedAsset
            ]);
        }

        return view('fixed-assets.show', compact('fixedAsset', 'depreciationSchedule'));
    }

    public function edit(FixedAsset $fixedAsset)
    {
        $assetAccounts = TrialBalance::where('level', 4)
            ->whereHas('parent', function($query) {
                $query->where('is_aset', 1);
            })
            ->orderBy('kode')
            ->get();
        $allAccounts = TrialBalance::orderBy('kode')->get();

        return view('fixed-assets.edit', compact('fixedAsset', 'assetAccounts', 'allAccounts'));
    }

    public function update(UpdateFixedAssetRequest $request, FixedAsset $fixedAsset)
    {
        $validated = $request->validated();
        $validated['is_active'] = $validated['status'] === 'active';
        
        $fixedAsset->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fixed asset updated successfully',
                'data' => $fixedAsset->load(['assetAccount', 'accumulatedAccount', 'expenseAccount'])
            ]);
        }

        return redirect()->route('fixed-assets.show', $fixedAsset)
            ->with('success', 'Aset tetap berhasil diupdate');
    }

    public function destroy(FixedAsset $fixedAsset)
    {
        if ($fixedAsset->depreciations()->exists()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete asset with posted depreciations'
                ], 422);
            }

            return back()->with('error', 'Tidak dapat menghapus aset yang sudah memiliki penyusutan');
        }

        $fixedAsset->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fixed asset deleted successfully'
            ]);
        }

        return redirect()->route('fixed-assets.index')
            ->with('success', 'Aset tetap berhasil dihapus');
    }
}