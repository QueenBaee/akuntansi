<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLedgerRequest;
use App\Http\Requests\UpdateLedgerRequest;
use App\Models\Ledger;
use App\Services\LedgerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    public function index()
    {
        $ledgers = $this->ledgerService->getAllLedgers();
        return view('ledger.index', compact('ledgers'));
    }

    public function store(StoreLedgerRequest $request): JsonResponse
    {
        try {
            $ledger = $this->ledgerService->createLedger($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Ledger berhasil ditambahkan',
                'data' => $ledger
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan ledger: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateLedgerRequest $request, Ledger $ledger): JsonResponse
    {
        try {
            $updatedLedger = $this->ledgerService->updateLedger($ledger, $request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Ledger berhasil diupdate',
                'data' => $updatedLedger
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate ledger: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Ledger $ledger): JsonResponse
    {
        try {
            $this->ledgerService->deleteLedger($ledger);
            return response()->json([
                'success' => true,
                'message' => 'Ledger berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus ledger: ' . $e->getMessage()
            ], 500);
        }
    }
}