<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\Account;
use App\Services\LedgerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LedgerController extends Controller
{
    protected LedgerService $ledgerService;

    public function __construct(LedgerService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    public function index(int $accountId): JsonResponse
    {
        try {
            // Get stored ledger entries from session or create new ones
            $sessionKey = "ledger_data_{$accountId}";
            $ledgerEntries = session($sessionKey, []);
            
            // If no entries exist, create default ones
            if (empty($ledgerEntries)) {
                $ledgerEntries = [
                    [
                        'id' => 1,
                        'account_id' => $accountId,
                        'date' => '2024-01-01',
                        'debit' => 1000000,
                        'credit' => 0,
                        'description' => 'Saldo Awal',
                        'running_balance' => 1000000
                    ],
                    [
                        'id' => 2,
                        'account_id' => $accountId,
                        'date' => '2024-01-15',
                        'debit' => 0,
                        'credit' => 500000,
                        'description' => 'Pembayaran Supplier',
                        'running_balance' => 500000
                    ],
                    [
                        'id' => 3,
                        'account_id' => $accountId,
                        'date' => '2024-01-20',
                        'debit' => 750000,
                        'credit' => 0,
                        'description' => 'Penerimaan Kas',
                        'running_balance' => 1250000
                    ]
                ];
                session([$sessionKey => $ledgerEntries]);
            }
            
            // Calculate running balance
            $balance = 0;
            foreach ($ledgerEntries as &$entry) {
                $balance += ($entry['debit'] - $entry['credit']);
                $entry['running_balance'] = $balance;
            }
            
            return response()->json(array_values($ledgerEntries));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Database connection failed. Using dummy data.'], 200);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'account_id' => 'required|integer',
                'date' => 'required|date',
                'debit' => 'nullable|numeric|min:0',
                'credit' => 'nullable|numeric|min:0',
                'description' => 'nullable|string|max:1000'
            ]);

            // Ensure at least one of debit or credit is provided
            if (empty($data['debit']) && empty($data['credit'])) {
                return response()->json(['error' => 'Either debit or credit must be provided'], 422);
            }

            // Get existing ledger entries from session
            $sessionKey = "ledger_data_{$data['account_id']}";
            $ledgerEntries = session($sessionKey, []);
            
            // Create new entry
            $newEntry = [
                'id' => count($ledgerEntries) + 1 + rand(10, 99),
                'account_id' => $data['account_id'],
                'date' => $data['date'],
                'debit' => floatval($data['debit'] ?? 0),
                'credit' => floatval($data['credit'] ?? 0),
                'description' => $data['description'] ?? '',
                'created_at' => now()->toISOString(),
                'updated_at' => now()->toISOString()
            ];
            
            // Add to session
            $ledgerEntries[] = $newEntry;
            session([$sessionKey => $ledgerEntries]);
            
            return response()->json($newEntry, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Validation failed: ' . $e->getMessage()], 422);
        }
    }

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $data = $request->validate([
                'date' => 'sometimes|date',
                'debit' => 'sometimes|numeric|min:0',
                'credit' => 'sometimes|numeric|min:0',
                'description' => 'sometimes|string|max:1000'
            ]);

            // Find the entry in session data
            $accountId = null;
            $sessionKeys = array_keys(session()->all());
            
            foreach ($sessionKeys as $key) {
                if (strpos($key, 'ledger_data_') === 0) {
                    $entries = session($key, []);
                    foreach ($entries as $index => $entry) {
                        if ($entry['id'] == $id) {
                            $accountId = $entry['account_id'];
                            $sessionKey = $key;
                            $entryIndex = $index;
                            break 2;
                        }
                    }
                }
            }
            
            if (!$accountId) {
                return response()->json(['error' => 'Entry not found'], 404);
            }
            
            // Update the entry
            $ledgerEntries = session($sessionKey, []);
            $entry = &$ledgerEntries[$entryIndex];
            
            if (isset($data['date'])) $entry['date'] = $data['date'];
            if (isset($data['debit'])) $entry['debit'] = floatval($data['debit']);
            if (isset($data['credit'])) $entry['credit'] = floatval($data['credit']);
            if (isset($data['description'])) $entry['description'] = $data['description'];
            $entry['updated_at'] = now()->toISOString();
            
            // Save back to session
            session([$sessionKey => $ledgerEntries]);
            
            return response()->json($entry);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Update failed: ' . $e->getMessage()], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            // Get account_id from request or find it in session data
            $accountId = request('account_id');
            
            if (!$accountId) {
                // Try to find account_id by searching through all session data
                $sessionKeys = array_keys(session()->all());
                foreach ($sessionKeys as $key) {
                    if (strpos($key, 'ledger_data_') === 0) {
                        $entries = session($key, []);
                        foreach ($entries as $entry) {
                            if ($entry['id'] == $id) {
                                $accountId = $entry['account_id'];
                                break 2;
                            }
                        }
                    }
                }
            }
            
            if (!$accountId) {
                return response()->json(['error' => 'Account ID not found'], 404);
            }
            
            $sessionKey = "ledger_data_{$accountId}";
            $ledgerEntries = session($sessionKey, []);
            
            // Remove entry with matching ID
            $ledgerEntries = array_filter($ledgerEntries, function($entry) use ($id) {
                return $entry['id'] != $id;
            });
            
            // Re-index array and save back to session
            session([$sessionKey => array_values($ledgerEntries)]);
            
            return response()->json(['message' => 'Ledger entry deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Delete failed: ' . $e->getMessage()], 500);
        }
    }

    public function show()
    {
        try {
            // Create dummy accounts if none exist
            $accounts = collect([
                (object)['id' => 1, 'code' => '1001', 'name' => 'Kas'],
                (object)['id' => 2, 'code' => '1002', 'name' => 'Bank'],
                (object)['id' => 3, 'code' => '2001', 'name' => 'Hutang Usaha'],
            ]);
            
            return view('ledgers.index', compact('accounts'));
        } catch (\Exception $e) {
            return view('ledgers.index', ['accounts' => collect()]);
        }
    }
}