<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use App\Models\TrialBalance;
use App\Models\Journal;
use App\Services\JournalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetInProgressController extends Controller
{
    protected $journalService;

    public function __construct(JournalService $journalService)
    {
        $this->journalService = $journalService;
    }

    public function index()
    {
        $assets = FixedAsset::inProgress()
            ->with(['assetAccount', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Group assets by acquisition account, then by group
        $groupedAssets = $assets->groupBy(function($asset) {
            if ($asset->assetAccount) {
                return $asset->assetAccount->keterangan ?: $asset->assetAccount->nama;
            }
            return 'ADP - ' . ($asset->group ?: 'Tidak Dikelompokkan');
        })->map(function($accountAssets) {
            return $accountAssets->groupBy('group');
        });

        return view('assets-in-progress.index', compact('groupedAssets'));
    }

    public function show(FixedAsset $asset)
    {
        if ($asset->group !== 'Aset Dalam Penyelesaian') {
            return redirect()->route('assets-in-progress.index')
                ->with('error', 'Asset is not in progress');
        }

        $asset->load(['assetAccount', 'creator', 'journals']);
        
        return view('assets-in-progress.show', compact('asset'));
    }

    public function showReclassify(Request $request)
    {
        $assetIds = explode(',', $request->get('assets', ''));
        $selectedAssets = FixedAsset::whereIn('id', $assetIds)
            ->where('group', 'Aset Dalam Penyelesaian')
            ->where('is_converted', false)
            ->get();

        if ($selectedAssets->isEmpty()) {
            return redirect()->route('assets-in-progress.index')
                ->with('error', 'No valid assets selected for reclassification');
        }

        $totalPrice = $selectedAssets->sum('acquisition_price');
        $suggestedName = $selectedAssets->pluck('name')->join(' + ');
        $suggestedCode = FixedAsset::generateAssetNumber();
        $earliestDate = $selectedAssets->min('acquisition_date')->format('Y-m-d');

        $assetAccounts = TrialBalance::where('level', 4)
            ->whereHas('parent', function($query) {
                $query->where('is_aset', 1);
            })
            ->where('kode', 'not like', 'A22%') // Exclude ADP accounts
            ->orderBy('kode')
            ->get();
        
        $accumulatedAccounts = TrialBalance::where('kode', 'like', 'A24%')->orderBy('kode')->get();
        $expenseAccounts = TrialBalance::where('kode', 'like', 'E22%')->orderBy('kode')->get();

        return view('assets-in-progress.reclassify', compact(
            'selectedAssets', 'totalPrice', 'suggestedName', 'suggestedCode', 
            'earliestDate', 'assetAccounts', 'accumulatedAccounts', 'expenseAccounts'
        ));
    }

    public function reclassify(Request $request)
    {
        $request->validate([
            'asset_ids' => 'required|string',
            'code' => 'required|string|max:50|unique:fixed_assets,code',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'group' => 'required|in:Permanent,Non-permanent,Group 1,Group 2',
            'condition' => 'required|in:Baik,Rusak',
            'status' => 'required|in:active,inactive',
            'acquisition_date' => 'required|date',
            'acquisition_price' => 'required|numeric|min:0',
            'depreciation_method' => 'required|in:garis lurus,saldo menurun',
            'depreciation_start_date' => 'required|date',
            'useful_life_years' => 'required|integer|min:1|max:50',
            'asset_account_id' => 'required|exists:trial_balances,id',
            'accumulated_account_id' => 'required|exists:trial_balances,id',
            'expense_account_id' => 'required|exists:trial_balances,id'
        ]);

        $assetIds = explode(',', $request->asset_ids);
        $assetsToReclassify = FixedAsset::whereIn('id', $assetIds)
            ->where('group', 'Aset Dalam Penyelesaian')
            ->where('is_converted', false)
            ->get();

        if ($assetsToReclassify->count() !== count($assetIds)) {
            return back()->with('error', 'Some assets are not available for reclassification');
        }

        $newAsset = DB::transaction(function () use ($request, $assetsToReclassify) {
            // Create new regular asset
            $newAsset = FixedAsset::create([
                'code' => $request->code,
                'name' => $request->name,
                'quantity' => $request->quantity,
                'location' => $request->location,
                'group' => $request->group,
                'condition' => $request->condition,
                'status' => $request->status,
                'acquisition_date' => $request->acquisition_date,
                'acquisition_price' => $request->acquisition_price,
                'residual_value' => 1,
                'depreciation_method' => $request->depreciation_method,
                'depreciation_start_date' => $request->depreciation_start_date,
                'useful_life_years' => $request->useful_life_years,
                'useful_life_months' => $request->useful_life_years * 12,
                'asset_account_id' => $request->asset_account_id,
                'accumulated_account_id' => $request->accumulated_account_id,
                'expense_account_id' => $request->expense_account_id,
                'depreciation_rate' => $request->depreciation_method === 'garis lurus' 
                    ? round(100 / $request->useful_life_years, 2)
                    : round((2 / $request->useful_life_years) * 100, 2),
                'is_active' => $request->status === 'active',
                'created_by' => auth()->id()
            ]);

            // Mark old assets as converted
            foreach ($assetsToReclassify as $asset) {
                $asset->update([
                    'is_converted' => true,
                    'parent_asset_id' => $newAsset->id,
                    'converted_at' => now(),
                    'converted_by' => auth()->id()
                ]);
            }

            // Create reclassification journal
            $this->createReclassificationJournal($assetsToReclassify, $newAsset);

            return $newAsset;
        });

        return redirect()->route('fixed-assets.show', $newAsset)
            ->with('success', 'Assets successfully reclassified to regular asset');
    }

    protected function createReclassificationJournal($assetsInProgress, $newAsset)
    {
        $totalAmount = $assetsInProgress->sum('acquisition_price');
        
        // Get ADP account (first asset's account)
        $adpAccount = $assetsInProgress->first()->assetAccount;
        
        // Create journal entry without details array
        $journal = Journal::create([
            'date' => now()->format('Y-m-d'),
            'number' => 'REKLAS-' . $newAsset->code . '-' . now()->format('Ymd'),
            'description' => 'Reklasifikasi Aset Dalam Penyelesaian ke ' . $newAsset->name,
            'reference' => 'REKLAS-' . $newAsset->code,
            'pic' => auth()->user()->name,
            'cash_in' => $totalAmount,
            'cash_out' => $totalAmount,
            'source_module' => 'memorial',
            'source_id' => $newAsset->id,
            'fixed_asset_id' => $newAsset->id,
            'debit_account_id' => $newAsset->asset_account_id, // HP - Jenis Aset
            'credit_account_id' => $adpAccount->id, // ADP - Jenis Aset
            'total_debit' => $totalAmount,
            'total_credit' => $totalAmount,
            'is_posted' => true,
            'created_by' => auth()->id()
        ]);

        return $journal;
    }
}