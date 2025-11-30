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
        
        $accounts = TrialBalance::where(function($q) use ($query) {
            $q->where('kode', 'LIKE', "%{$query}%")
              ->orWhere('keterangan', 'LIKE', "%{$query}%");
        })
        ->limit(20)
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