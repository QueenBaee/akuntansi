<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\TrialBalance;
use App\Http\Requests\StoreFixedAssetRequest;
use App\Services\FixedAssetService;
use Illuminate\Http\Request;

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
            
        // Add sample data if no assets exist
        if (FixedAsset::count() == 0) {
            \Artisan::call('db:seed', ['--class' => 'FixedAssetSampleSeeder']);
        }

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
            // Add category codes to assets
            $assetsData = $assets->getCollection()->map(function ($asset) {
                $name = strtolower($asset->name);
                if (strpos($name, 'gedung') !== false || strpos($name, 'kantor') !== false) {
                    $asset->category_kode = '1.1';
                } elseif (strpos($name, 'gudang') !== false) {
                    $asset->category_kode = '1.2';
                } elseif (strpos($name, 'avanza') !== false || strpos($name, 'mobil') !== false) {
                    $asset->category_kode = '2.1';
                } elseif (strpos($name, 'vario') !== false || strpos($name, 'motor') !== false) {
                    $asset->category_kode = '2.2';
                } elseif (strpos($name, 'laptop') !== false || strpos($name, 'komputer') !== false) {
                    $asset->category_kode = '3.1';
                } elseif (strpos($name, 'mesin') !== false) {
                    $asset->category_kode = '3.2';
                } else {
                    $asset->category_kode = null;
                }
                return $asset;
            });
            
            $assets->setCollection($assetsData);
            
            return response()->json([
                'success' => true,
                'data' => $assets
            ]);
        }

        return view('fixed-assets.index', compact('assets'));
    }

    public function create()
    {
        $trialBalances = TrialBalance::orderBy('kode')->get();
        $parentAssets = FixedAsset::active()->get(['id', 'name', 'code']);
        
        // Static categories for now
        $categories = collect([
            ['kode' => '1', 'nama' => 'Bangunan', 'level' => 1],
            ['kode' => '1.1', 'nama' => 'Gedung Kantor', 'level' => 2],
            ['kode' => '1.2', 'nama' => 'Gudang', 'level' => 2],
            ['kode' => '1.3', 'nama' => 'Pabrik', 'level' => 2],
            ['kode' => '2', 'nama' => 'Kendaraan', 'level' => 1],
            ['kode' => '2.1', 'nama' => 'Mobil', 'level' => 2],
            ['kode' => '2.2', 'nama' => 'Motor', 'level' => 2],
            ['kode' => '2.3', 'nama' => 'Truk', 'level' => 2],
            ['kode' => '3', 'nama' => 'Peralatan', 'level' => 1],
            ['kode' => '3.1', 'nama' => 'Komputer', 'level' => 2],
            ['kode' => '3.2', 'nama' => 'Mesin', 'level' => 2],
            ['kode' => '3.3', 'nama' => 'Furniture', 'level' => 2],
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'trialBalances' => $trialBalances,
                    'parentAssets' => $parentAssets,
                    'categories' => $categories
                ]
            ]);
        }

        return view('fixed-assets.create', compact('trialBalances', 'parentAssets'));
    }

    public function store(StoreFixedAssetRequest $request)
    {
        $asset = FixedAsset::create([
            ...$request->validated(),
            'residual_value' => $request->residual_value ?? 0,
            'created_by' => auth()->id(),
        ]);

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
        $trialBalances = TrialBalance::orderBy('kode')->get();
        $parentAssets = FixedAsset::active()->where('id', '!=', $fixedAsset->id)->get(['id', 'name', 'code']);

        return view('fixed-assets.edit', compact('fixedAsset', 'trialBalances', 'parentAssets'));
    }

    public function update(\App\Http\Requests\UpdateFixedAssetRequest $request, FixedAsset $fixedAsset)
    {
        $fixedAsset->update([
            ...$request->validated(),
            'residual_value' => $request->residual_value ?? 0,
        ]);

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
        // Check if asset has posted depreciations
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
    


    public function storeMutation(Request $request, FixedAsset $fixedAsset)
    {
        $validated = $request->validate([
            'type' => 'required|in:addition,disposal',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:500',
        ]);

        $mutation = $fixedAsset->mutations()->create([
            ...$validated,
            'created_by' => auth()->id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Asset mutation created successfully',
                'data' => $mutation
            ], 201);
        }

        return back()->with('success', 'Mutasi aset berhasil ditambahkan');
    }
}