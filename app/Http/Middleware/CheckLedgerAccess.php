<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Ledger;

class CheckLedgerAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        // Admin has access to all ledgers
        if ($user->hasRole('admin')) {
            return $next($request);
        }

        // Get ledger ID from route parameter
        $ledgerId = $request->route('ledger') 
            ? $request->route('ledger')->id 
            : $request->route('id');

        if ($ledgerId) {
            // Check if user has access to this ledger
            $hasAccess = $user->activeLedgers()->where('ledgers.id', $ledgerId)->exists();
            
            if (!$hasAccess) {
                abort(403, 'You do not have access to this ledger.');
            }
        }

        return $next($request);
    }
}