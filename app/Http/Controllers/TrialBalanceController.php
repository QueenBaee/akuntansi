<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TrialBalance;
use Illuminate\Http\Request;

class TrialBalanceController extends Controller
{
    public function index(Request $request)
    {
        $query = TrialBalance::with('children')->whereNull('parent_id')->orderBy('kode');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search){
                $q->where('kode', 'like', "%$search%")
                ->orWhere('keterangan', 'like', "%$search%");
            });
        }

        $items = $query->with('children')->get();

        // Ambil Beban (E) untuk group
        $bebanItems = TrialBalance::where('kode', 'like', 'E%')
            ->orderBy('kode')
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
            'kode' => 'required',
            'keterangan' => 'required',
            'parent_id' => 'nullable|exists:trial_balances,id',
            'tahun_2024' => 'nullable|numeric',
        ]);

        $level = $request->parent_id ? TrialBalance::find($request->parent_id)->level + 1 : 1;

        TrialBalance::create([
            'kode' => $request->kode,
            'keterangan' => $request->keterangan,
            'parent_id' => $request->parent_id,
            'level' => $level,
            'tahun_2024' => $request->tahun_2024,
        ]);

        return redirect()->route('trial-balance.index')->with('success', 'Berhasil ditambahkan.');
    }

    public function edit(TrialBalance $trial_balance)
    {
        return view('trial_balance.edit', compact('trial_balance'));
    }

    public function update(Request $request, TrialBalance $trial_balance)
    {
        $request->validate([
            'kode' => 'required',
            'keterangan' => 'required',
            'tahun_2024' => 'nullable|numeric'
        ]);

        $trial_balance->update([
            'kode' => $request->kode,
            'keterangan' => $request->keterangan,
            'tahun_2024' => $request->tahun_2024
        ]);

        return redirect()->route('trial-balance.index')->with('success', 'Berhasil diupdate.');
    }

    public function destroy(TrialBalance $trial_balance)
    {
        $trial_balance->delete();
        return redirect()->route('trial-balance.index')->with('success', 'Berhasil dihapus.');
    }
}