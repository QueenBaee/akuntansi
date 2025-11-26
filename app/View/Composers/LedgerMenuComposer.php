<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Models\Ledger;

class LedgerMenuComposer
{
    public function compose(View $view)
    {
        $user = auth()->user();
        
        if (!$user) {
            $view->with([
                'userLedgers' => collect(),
                'cashLedgers' => collect(),
                'bankLedgers' => collect()
            ]);
            return;
        }

        // Get user's accessible ledgers
        if ($user->hasRole('admin')) {
            // Admin sees all active ledgers
            $userLedgers = Ledger::where('is_active', true)->get();
        } else {
            // Regular users see only assigned ledgers
            $userLedgers = $user->activeLedgers;
        }

        // Separate by type
        $cashLedgers = $userLedgers->where('tipe_ledger', 'kas');
        $bankLedgers = $userLedgers->where('tipe_ledger', 'bank');

        $view->with([
            'userLedgers' => $userLedgers,
            'cashLedgers' => $cashLedgers,
            'bankLedgers' => $bankLedgers
        ]);
    }
}