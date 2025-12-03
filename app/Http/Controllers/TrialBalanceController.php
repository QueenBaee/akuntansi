<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TrialBalance;
use Illuminate\Http\Request;

class TrialBalanceController extends Controller
{
    public function index(Request $request)
    {
        $query = TrialBalance::with('children')->whereNull('parent_id')->orderBy('sort_order');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search){
                $q->where('kode', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%");
            });
        }

        // Filter kas/bank accounts
        if ($request->filled('filter_kas_bank')) {
            $isKasBank = $request->filter_kas_bank === '1';
            
            // Ambil semua ID yang cocok dengan filter (termasuk parent dari yang cocok)
            $matchingIds = collect();
            
            // Cari semua akun yang langsung cocok
            $directMatches = TrialBalance::where('is_kas_bank', $isKasBank)->pluck('id');
            $matchingIds = $matchingIds->merge($directMatches);
            
            // Cari parent dari akun yang cocok
            foreach ($directMatches as $id) {
                $item = TrialBalance::find($id);
                while ($item && $item->parent_id) {
                    $matchingIds->push($item->parent_id);
                    $item = $item->parent;
                }
            }
            
            $query->whereIn('id', $matchingIds->unique());
        }

        $items = $query->with(['children' => function($query) {
            $query->orderBy('sort_order')->with(['children' => function($subQuery) {
                $subQuery->orderBy('sort_order')->with(['children' => function($subSubQuery) {
                    $subSubQuery->orderBy('sort_order');
                }]);
            }]);
        }])->get();

        // Filter items berdasarkan kas/bank jika ada filter
        if ($request->filled('filter_kas_bank')) {
            $isKasBank = $request->filter_kas_bank === '1';
            $items = $this->filterItemsByKasBank($items, $isKasBank);
        }

        // Ambil Beban (E) untuk group
        $bebanItems = TrialBalance::where('kode', 'like', 'E%')
            ->orderBy('sort_order')
            ->get()
            ->groupBy(function($item){
                return substr($item->kode, 0, 2); // E1, E2, E3, E9
            });

        return view('trial_balance.index', compact('items', 'bebanItems'));
    }

    // Recursive function for flatten hierarchy
    private function flatten($items, $prefix = '')
    {
        $result = [];
        foreach ($items as $item) {
            $result[] = ['item' => $item, 'prefix' => $prefix];
            if ($item->children->count() > 0) {
                $result = array_merge($result, $this->flatten($item->children, $prefix . '    ')); // spasi indentasi
            }
        }
        return $result;
    }

    public function create(Request $request)
    {
        // Get parent_id from query string if you want to add child  
        $parent_id = $request->query('parent_id');
        $parent = $parent_id ? TrialBalance::find($parent_id) : null;

        return view('trial_balance.create', compact('parent'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:trial_balances,kode',
            'keterangan' => 'required',
            'parent_id' => 'nullable|exists:trial_balances,id',
            'tahun_2024' => 'nullable|numeric',
            'is_kas_bank' => 'nullable|boolean'
        ]);

        $level = $request->parent_id ? TrialBalance::find($request->parent_id)->level + 1 : 1;
        
        // Tentukan sort_order berdasarkan parent atau level
        $sortOrder = 1;
        if ($request->parent_id) {
            $lastChild = TrialBalance::where('parent_id', $request->parent_id)
                ->orderBy('sort_order', 'desc')
                ->first();
            $sortOrder = $lastChild ? $lastChild->sort_order + 1 : 1;
        } else {
            $lastRoot = TrialBalance::whereNull('parent_id')
                ->orderBy('sort_order', 'desc')
                ->first();
            $sortOrder = $lastRoot ? $lastRoot->sort_order + 1 : 1;
        }

        TrialBalance::create([
            'kode' => $request->kode,
            'keterangan' => $request->keterangan,
            'parent_id' => $request->parent_id,
            'level' => $level,
            'sort_order' => $sortOrder,
            'tahun_2024' => $request->tahun_2024,
            'is_kas_bank' => $request->boolean('is_kas_bank'),
        ]);

        return redirect()->route('trial-balance.index')->with('success', 'Trial Balance berhasil ditambahkan.');
    }

    public function edit(TrialBalance $trial_balance)
    {
        return view('trial_balance.edit', compact('trial_balance'));
    }

    public function update(Request $request, TrialBalance $trial_balance)
    {
        $request->validate([
            'kode' => 'required|unique:trial_balances,kode,' . $trial_balance->id,
            'keterangan' => 'required',
            'tahun_2024' => 'nullable|numeric',
            'is_kas_bank' => 'nullable|boolean'
        ]);

        $trial_balance->update([
            'kode' => $request->kode,
            'keterangan' => $request->keterangan,
            'tahun_2024' => $request->tahun_2024,
            'is_kas_bank' => $request->boolean('is_kas_bank'),
        ]);

        return redirect()->route('trial-balance.index')->with('success', 'Trial Balance berhasil diupdate.');
    }

    public function destroy(TrialBalance $trial_balance)
    {
        // Cek apakah memiliki anak
        if ($trial_balance->children()->count() > 0) {
            return redirect()->route('trial-balance.index')
                ->with('error', 'Tidak dapat menghapus akun yang memiliki sub akun.');
        }
        
        $trial_balance->delete();
        return redirect()->route('trial-balance.index')->with('success', 'Trial Balance berhasil dihapus.');
    }
    
    /**
     * Filter items berdasarkan kas/bank
     */
    private function filterItemsByKasBank($items, $isKasBank)
    {
        return $items->filter(function($item) use ($isKasBank) {
            // Jika item ini cocok
            if ($item->is_kas_bank == $isKasBank) {
                return true;
            }
            
            // Jika ada anak yang cocok
            return $this->hasMatchingKasBankChildren($item, $isKasBank);
        })->map(function($item) use ($isKasBank) {
            // Filter children juga
            $item->children = $this->filterItemsByKasBank($item->children, $isKasBank);
            return $item;
        });
    }
    
    /**
     * Cek apakah ada anak yang cocok dengan filter kas/bank
     */
    private function hasMatchingKasBankChildren($item, $isKasBank)
    {
        foreach ($item->children as $child) {
            if ($child->is_kas_bank == $isKasBank || $this->hasMatchingKasBankChildren($child, $isKasBank)) {
                return true;
            }
        }
        return false;
    }

    /**
     * AJAX endpoint for trial balance data
     */
    public function getData(Request $request)
    {
        try {
            $year = $request->year ?? date('Y');
            $items = TrialBalance::orderBy('sort_order')->get();

            $data = [];
            foreach ($items as $item) {
                $data[$item->id] = [
                    'month_1' => 0, 'month_2' => 0, 'month_3' => 0, 'month_4' => 0,
                    'month_5' => 0, 'month_6' => 0, 'month_7' => 0, 'month_8' => 0,
                    'month_9' => 0, 'month_10' => 0, 'month_11' => 0, 'month_12' => 0,
                    'total' => 0, 'opening' => 0
                ];
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'items' => $items,
                    'data' => $data,
                    'year' => $year
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load trial balance data: ' . $e->getMessage()
            ], 500);
        }
    }
}