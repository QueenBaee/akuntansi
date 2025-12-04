<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrialBalance;
use Illuminate\Http\Request;

class AccountSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $excludeKasBank = $request->get('exclude_kas_bank', false);
        
        $accounts = TrialBalance::where(function($q) use ($query) {
            $q->where('kode', 'LIKE', "%{$query}%")
              ->orWhere('keterangan', 'LIKE', "%{$query}%");
        });
        
        // Exclude kas/bank accounts if requested
        if ($excludeKasBank) {
            $accounts->where(function($q) {
                $q->where('is_kas_bank', false)
                  ->orWhereNull('is_kas_bank');
            });
        }
        
        $accounts = $accounts->limit(20)
            ->get()
            ->map(function($account) {
                return [
                    'id' => $account->id,
                    'text' => $account->kode . ' - ' . $account->keterangan
                ];
            });

        return response()->json([
            'results' => $accounts
        ]);
    }
}