<?php

namespace App\Http\Controllers;

use App\Models\TrialBalance;
use Illuminate\Http\Request;

class TrialBalanceController extends Controller
{
    public function index(Request $request)
    {
        $query = TrialBalance::orderBy('kode');

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('kode', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('keterangan', 'LIKE', '%' . $request->search . '%');
            });
        }

        $items = $query->get();

        return view('trial_balance.index', compact('items'));
    }

    public function create()
    {
        $parents = TrialBalance::orderBy('kode')->get();

        return view('trial_balance.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required',
            'keterangan' => 'required',
            'level' => 'required|integer',
            'parent_id' => 'nullable|integer',
            'is_kas_bank' => 'nullable|in:kas,bank',
            'tahun_2024' => 'nullable|numeric', // ← TAMBAHKAN
        ]);


        TrialBalance::create($request->all());

        return redirect()->route('trial-balance.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $item = TrialBalance::findOrFail($id);
        $parents = TrialBalance::orderBy('kode')->get();

        return view('trial_balance.edit', compact('item', 'parents'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode' => 'required',
            'keterangan' => 'required',
            'level' => 'required|integer',
            'parent_id' => 'nullable|integer',
            'is_kas_bank' => 'nullable|in:kas,bank',
            'tahun_2024' => 'nullable|numeric', // ← TAMBAHKAN
        ]);


        $item = TrialBalance::findOrFail($id);
        $item->update($request->all());

        return redirect()->route('trial-balance.index')
            ->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        TrialBalance::findOrFail($id)->delete();

        return redirect()->route('trial-balance.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
