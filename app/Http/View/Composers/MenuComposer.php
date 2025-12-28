<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Ledger;
use Illuminate\Support\Facades\Auth;

class MenuComposer
{
    public function compose(View $view)
    {
        $user = Auth::user();
        
        if (!$user) {
            $view->with([
                'cashAccounts' => collect(),
                'bankAccounts' => collect(),
                'activeContext' => null,
            ]);
            return;
        }

        // Filter menu berdasarkan hak akses user
        if ($user->hasRole('admin')) {
            // Admin bisa akses semua ledger
            $cashAccounts = Ledger::where('is_active', true)
                ->where('tipe_ledger', 'kas')
                ->whereHas('trialBalance', function($query) {
                    $query->whereNotNull('tahun_2024')->where('tahun_2024', '!=', 0);
                })
                ->orderBy('trial_balance_id')
                ->get();
                
            $bankAccounts = Ledger::where('is_active', true)
                ->where('tipe_ledger', 'bank')
                ->whereHas('trialBalance', function($query) {
                    $query->whereNotNull('tahun_2024')->where('tahun_2024', '!=', 0);
                })
                ->orderBy('trial_balance_id')
                ->get();
        } else {
            // Non-admin hanya bisa akses ledger yang ada di user_ledgers
            $cashAccounts = $user->activeLedgers()
                ->where('tipe_ledger', 'kas')
                ->whereHas('trialBalance', function($query) {
                    $query->whereNotNull('tahun_2024')->where('tahun_2024', '!=', 0);
                })
                ->orderBy('trial_balance_id')
                ->get();
                
            $bankAccounts = $user->activeLedgers()
                ->where('tipe_ledger', 'bank')
                ->whereHas('trialBalance', function($query) {
                    $query->whereNotNull('tahun_2024')->where('tahun_2024', '!=', 0);
                })
                ->orderBy('trial_balance_id')
                ->get();
        }
        
        // Determine active navigation context
        $activeContext = $this->determineActiveContext();

        // Oper data ke view
        $view->with([
            'cashAccounts' => $cashAccounts,
            'bankAccounts' => $bankAccounts,
            'activeContext' => $activeContext,
            'isAdmin' => $user->hasRole('admin'),
        ]);
    }
    
    private function determineActiveContext()
    {
        $request = request();
        $route = $request->route();
        $routeName = $route ? $route->getName() : '';
        
        $context = [
            'route' => $routeName,
            'ledger_id' => $request->get('ledger_id'),
            'type' => null,
            'active_section' => null
        ];
        
        // Determine context based on route and parameters
        if ($routeName === 'journals.create' && $context['ledger_id']) {
            $ledger = Ledger::find($context['ledger_id']);
            if ($ledger) {
                $context['type'] = $ledger->tipe_ledger;
                $context['active_section'] = $ledger->tipe_ledger === 'kas' ? 'cash-account' : 'bank-account';
            }
        } elseif ($routeName === 'ledgers.cash') {
            $context['type'] = 'kas';
            $context['active_section'] = 'cash-account';
        } elseif ($routeName === 'ledgers.bank') {
            $context['type'] = 'bank';
            $context['active_section'] = 'bank-account';
        }
        
        return $context;
    }
}
