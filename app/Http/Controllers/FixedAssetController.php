<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\Journal;
use App\Models\TrialBalance;
use App\Http\Requests\StoreFixedAssetRequest;
use App\Http\Requests\UpdateFixedAssetRequest;
use Illuminate\Support\Facades\DB;
use App\Services\FixedAssetService;
use App\Services\AssetFromTransactionService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FixedAssetController extends Controller
{
    protected $assetService;
    protected $assetFromTransactionService;

    public function __construct(FixedAssetService $assetService, AssetFromTransactionService $assetFromTransactionService)
    {
        $this->assetService = $assetService;
        $this->assetFromTransactionService = $assetFromTransactionService;
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = FixedAsset::regularAssets()
            ->where('status', '!=', 'disposed')
            ->with(['assetAccount', 'accumulatedAccount', 'expenseAccount', 'creator'])
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

        $assets = $query->get();

        // Group assets by acquisition account, then by group
        $groupedAssets = $assets->groupBy(function($asset) {
            if ($asset->assetAccount) {
                return $asset->assetAccount->keterangan ?: $asset->assetAccount->nama;
            }
            return 'HP - ' . ($asset->group ?: 'Tidak Dikelompokkan');
        })->map(function($accountAssets) {
            return $accountAssets->groupBy('group');
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $groupedAssets
            ]);
        }

        // Get accounts for modal dropdowns
        $accumulatedAccounts = TrialBalance::where('kode', 'like', 'A24%')->orderBy('kode')->get();
        $expenseAccounts = TrialBalance::where('kode', 'like', 'E22%')->orderBy('kode')->get();

        return view('fixed-assets.index', compact('groupedAssets', 'accumulatedAccounts', 'expenseAccounts'));
    }

    public function create()
    {
        // Redirect to create from transaction - no manual asset creation
        return redirect()->route('fixed-assets.create-from-transaction')
            ->with('info', 'Assets must be created from journal transactions');
    }

    public function store(StoreFixedAssetRequest $request)
    {
        // Redirect to create from transaction - no manual asset creation
        return redirect()->route('fixed-assets.create-from-transaction')
            ->with('info', 'Assets must be created from journal transactions');
    }

    public function show(FixedAsset $fixedAsset)
    {
        $fixedAsset->load([
            'assetAccount', 
            'accumulatedAccount', 
            'expenseAccount', 
            'creator',
            'mutations',
            'depreciations.journal',
            'convertedAssets' => function($query) {
                $query->with(['assetAccount', 'creator']);
            }
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

    public function createFromTransaction(Request $request)
    {
        $journalId = $request->get('journal_id');
        $journal = Journal::with(['debitAccount', 'creditAccount'])->findOrFail($journalId);
        
        if (!$this->assetFromTransactionService->canCreateAssetFromTransaction($journal)) {
            return redirect()->back()->with('error', 'Transaksi ini tidak dapat dikonversi menjadi aset tetap');
        }
        
        $assetAccount = $this->assetFromTransactionService->getAssetAccountFromTransaction($journal);
        $acquisitionValue = $journal->debitAccount && $journal->debitAccount->parent && $journal->debitAccount->parent->is_aset 
            ? $journal->total_debit 
            : $journal->total_credit;
        
        // Get asset accounts for dropdown
        $assetAccounts = TrialBalance::where('level', 4)
            ->whereHas('parent', function($query) {
                $query->where('is_aset', 1);
            })
            ->orderBy('kode')
            ->get();
            
        $allAccounts = TrialBalance::orderBy('kode')->get();
        
        // Pre-fill data
        $prefillData = [
            'asset_account_id' => $assetAccount->id,
            'acquisition_date' => $journal->date->format('Y-m-d'),
            'acquisition_price' => $acquisitionValue,
            'description' => $journal->description,
            'reference_number' => $journal->number
        ];
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'journal' => $journal,
                    'assetAccounts' => $assetAccounts,
                    'allAccounts' => $allAccounts,
                    'prefillData' => $prefillData
                ]
            ]);
        }
        
        return view('fixed-assets.create-from-transaction', compact('journal', 'assetAccounts', 'allAccounts', 'prefillData'));
    }
    
    public function storeFromTransaction(StoreFixedAssetRequest $request)
    {
        $journalId = $request->input('journal_id');
        $journal = Journal::findOrFail($journalId);
        
        $validated = $request->validated();
        $validated['residual_value'] = 1;
        $validated['is_active'] = $validated['status'] === 'active';
        
        // Set null for non-depreciable assets
        if (in_array($validated['group'], ['Aset Dalam Penyelesaian', 'Tanah'])) {
            $validated['useful_life_years'] = null;
            $validated['useful_life_months'] = null;
            $validated['depreciation_rate'] = null;
            $validated['depreciation_method'] = null;
            $validated['depreciation_start_date'] = null;
        }
        
        $asset = $this->assetFromTransactionService->createAssetFromTransaction($journal, $validated);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Fixed asset created from transaction successfully',
                'data' => $asset->load(['assetAccount', 'accumulatedAccount', 'expenseAccount'])
            ], 201);
        }
        
        return redirect()->route('fixed-assets.show', $asset)
            ->with('success', 'Aset tetap berhasil dibuat dari transaksi');
    }

    public function getUnconvertedTransactions()
    {
        $transactions = $this->assetFromTransactionService->getUnconvertedAssetTransactions();
        
        return response()->json([
            'success' => true,
            'count' => count($transactions),
            'transactions' => $transactions
        ]);
    }

    public function convertToRegular(FixedAsset $fixedAsset, Request $request)
    {
        if ($fixedAsset->group !== 'Aset Dalam Penyelesaian') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset is not under construction'
                ], 422);
            }
            return back()->with('error', 'Aset bukan dalam penyelesaian');
        }

        $request->validate([
            'group' => 'required|in:Permanent,Non-permanent,Group 1,Group 2',
            'useful_life_years' => 'required|integer|min:1|max:50',
            'depreciation_method' => 'required|in:Straight Line,Declining Balance',
            'depreciation_start_date' => 'required|date',
            'accumulated_account_id' => 'required|exists:trial_balances,id',
            'expense_account_id' => 'required|exists:trial_balances,id'
        ]);

        $fixedAsset->update([
            'group' => $request->group,
            'useful_life_years' => $request->useful_life_years,
            'useful_life_months' => $request->useful_life_years * 12,
            'depreciation_method' => $request->depreciation_method,
            'depreciation_start_date' => $request->depreciation_start_date,
            'accumulated_account_id' => $request->accumulated_account_id,
            'expense_account_id' => $request->expense_account_id,
            'depreciation_rate' => $request->depreciation_method === 'Straight Line' 
                ? round(100 / $request->useful_life_years, 2)
                : round((2 / $request->useful_life_years) * 100, 2)
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Asset converted to regular successfully',
                'data' => $fixedAsset->fresh()
            ]);
        }

        return redirect()->route('fixed-assets.show', $fixedAsset)
            ->with('success', 'Aset berhasil dikonversi menjadi aset regular');
    }

    public function showMergeConvert(Request $request)
    {
        return redirect()->route('assets-in-progress.reclassify', $request->all())
            ->with('info', 'Reclassification moved to Assets in Progress section');
    }

    public function mergeConvert(Request $request)
    {
        return redirect()->route('assets-in-progress.reclassify', $request->all())
            ->with('info', 'Reclassification moved to Assets in Progress section');
    }

    public function dispose(FixedAsset $fixedAsset, Request $request)
    {
        $request->validate([
            'disposal_date' => 'required|date'
        ]);

        if (!$fixedAsset->is_active) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Asset is already inactive'
                ], 422);
            }
            return back()->with('error', 'Aset sudah tidak aktif');
        }

        try {
            $result = $this->assetService->disposeAsset(
                $fixedAsset, 
                $request->disposal_date, 
                auth()->id()
            );

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Asset disposed successfully',
                    'data' => $result
                ]);
            }

            return redirect()->route('fixed-assets.index')
                ->with('success', 'Aset berhasil di-dispose dengan Memorial Account');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}