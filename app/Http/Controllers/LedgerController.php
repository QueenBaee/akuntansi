<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    public function index()
    {
        $ledgers = Ledger::orderBy('nama_ledger')->get();

        return view('ledgers.index', compact('ledgers'));
    }

    public function create()
    {
        return view('ledgers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_ledger' => 'required|string|max:255',
            'kode_ledger' => 'required|string|unique:ledgers',
            'tipe_ledger' => 'required|in:kas,bank',
            'deskripsi' => 'nullable|string'
        ]);

        $ledger = Ledger::create($validated);

        return $request->expectsJson()
            ? response()->json([
                'success' => true,
                'message' => 'Ledger berhasil ditambahkan',
                'data' => $ledger
            ], 201)
            : redirect()->route('ledgers.index')->with('success', 'Ledger berhasil ditambahkan');
    }

    public function show(Ledger $ledger)
    {
        return request()->expectsJson()
            ? response()->json([
                'success' => true,
                'data' => $ledger
            ])
            : view('ledgers.show', compact('ledger'));
    }

    public function edit(Ledger $ledger)
    {
        return view('ledgers.edit', compact('ledger'));
    }

    public function update(Request $request, Ledger $ledger)
    {
        $validated = $request->validate([
            'nama_ledger' => 'required|string|max:255',
            'kode_ledger' => 'required|string|unique:ledgers,kode_ledger,' . $ledger->id,
            'tipe_ledger' => 'required|in:kas,bank',
            'deskripsi' => 'nullable|string',
            'is_active' => 'sometimes|boolean'
        ]);

        // pastikan unchecked checkbox = false
        $validated['is_active'] = $request->boolean('is_active');

        $ledger->update($validated);

        return $request->expectsJson()
            ? response()->json([
                'success' => true,
                'message' => 'Ledger berhasil diupdate',
                'data' => $ledger->fresh()
            ])
            : redirect()->route('ledgers.index')->with('success', 'Ledger berhasil diupdate');
    }

    public function destroy(Ledger $ledger)
    {
        // optional safety: cegah delete ledger yg terhubung jurnal
        if ($ledger->journals()->exists()) {
            return request()->expectsJson()
                ? response()->json([
                    'success' => false,
                    'message' => 'Ledger tidak dapat dihapus karena sedang digunakan.'
                ], 409)
                : back()->with('error', 'Ledger tidak dapat dihapus karena sedang digunakan.');
        }

        $ledger->delete();

        return request()->expectsJson()
            ? response()->json([
                'success' => true,
                'message' => 'Ledger berhasil dihapus'
            ])
            : redirect()->route('ledgers.index')->with('success', 'Ledger berhasil dihapus');
    }
}
