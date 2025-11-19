<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Account; // contoh model, sesuaikan dengan kebutuhan

class MenuComposer
{
    public function compose(View $view)
    {
        // Ambil data untuk menu
        $cashAccounts = Account::where('type', 'kas')->get();
        $bankAccounts = Account::where('type', 'bank')->get();

        // Oper data ke view
        $view->with([
            'cashAccounts' => $cashAccounts,
            'bankAccounts' => $bankAccounts,
        ]);
    }
}
