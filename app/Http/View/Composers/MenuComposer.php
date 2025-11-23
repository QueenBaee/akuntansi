<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Ledger;

class MenuComposer
{
    public function compose(View $view)
    {
        // Ambil data untuk menu
        $cashAccounts = Ledger::where('is_active', true)->where('tipe_ledger', 'kas')->orderBy('nama_ledger')->get();
        $bankAccounts = Ledger::where('is_active', true)->where('tipe_ledger', 'bank')->orderBy('nama_ledger')->get();

        // Oper data ke view
        $view->with([
            'cashAccounts' => $cashAccounts,
            'bankAccounts' => $bankAccounts,
        ]);
    }
}
