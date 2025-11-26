<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrialBalanceController;
use App\Http\Controllers\CashflowController;
use App\Http\Controllers\CashBankJournalController;
use App\Http\Controllers\CashAccountController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\TrialBalanceReportController;

// Guest routes (login)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');

    Route::post('/login', function (\Illuminate\Http\Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    });
});

// Logout
Route::middleware('auth')->post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Dashboard
Route::middleware('auth')->get('/', function () {
    return view('dashboard.index');
})->name('dashboard');

// Protected routes
Route::middleware('auth')->group(function () {

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', \App\Http\Controllers\Web\UserController::class);
        Route::resource('accounts', \App\Http\Controllers\Web\AccountController::class);
        Route::resource('user-accounts', \App\Http\Controllers\Web\UserAccountController::class);
        
        // User Ledgers (master data) - SPA routes
        Route::get('user-ledgers', [\App\Http\Controllers\Web\UserLedgerController::class, 'index'])->name('user-ledgers.index');
        Route::get('user-ledgers/create', [\App\Http\Controllers\UserLedgerController::class, 'create']);
        Route::post('user-ledgers', [\App\Http\Controllers\UserLedgerController::class, 'store']);
        Route::get('user-ledgers/data', [\App\Http\Controllers\UserLedgerController::class, 'index']);
        Route::get('user-ledgers/{userLedger}/edit', [\App\Http\Controllers\UserLedgerController::class, 'edit']);
        Route::put('user-ledgers/{userLedger}', [\App\Http\Controllers\UserLedgerController::class, 'update']);
        Route::delete('user-ledgers/{userLedger}', [\App\Http\Controllers\UserLedgerController::class, 'destroy']);
        
        // Cash Accounts
        Route::resource('cash-accounts', CashAccountController::class);
        Route::resource('bank-accounts', BankAccountController::class);
        
        // Ledgers
        Route::resource('ledgers', LedgerController::class)->middleware('ledger.access');
        
        // Ledgers with type filter
        Route::get('ledgers-cash', [LedgerController::class, 'index'])->name('ledgers.cash');
        Route::get('ledgers-bank', [LedgerController::class, 'index'])->name('ledgers.bank');
    });

    // Cash Transactions
    Route::resource('cash-transactions', \App\Http\Controllers\Web\CashTransactionController::class);

    // Journals
    Route::get('journals/create', [CashBankJournalController::class, 'create'])->name('journals.create');
    Route::post('journals', [CashBankJournalController::class, 'store'])->name('journals.store');
    Route::put('journals/{id}', [CashBankJournalController::class, 'update'])->name('journals.update');
    Route::get('journals/{id}/attachments', [CashBankJournalController::class, 'getAttachments'])->name('journals.attachments');
    Route::delete('journals/{id}', [CashBankJournalController::class, 'destroy'])->name('journals.destroy');
    Route::resource('journals', \App\Http\Controllers\Web\JournalController::class)->except(['create','store','destroy']);

    // Memorials
    Route::get('memorials/create', [\App\Http\Controllers\MemorialController::class, 'create'])->name('memorials.create');
    Route::post('memorials', [\App\Http\Controllers\MemorialController::class, 'store'])->name('memorials.store');
    Route::put('memorials/{id}', [\App\Http\Controllers\MemorialController::class, 'update'])->name('memorials.update');
    Route::get('memorials/{id}/attachments', [\App\Http\Controllers\MemorialController::class, 'getAttachments'])->name('memorials.attachments');
    Route::delete('memorials/{id}', [\App\Http\Controllers\MemorialController::class, 'destroy'])->name('memorials.destroy');
    Route::resource('memorials', \App\Http\Controllers\Web\MemorialController::class)->except(['create','store','destroy']);

    // Trial Balance
    Route::resource('trial-balance', TrialBalanceController::class);

    // Cashflow
    Route::resource('cashflow', CashflowController::class);

    // Trial Balance Report (pastikan sebelum catch-all)
    Route::get('/trial-balance-report', [TrialBalanceReportController::class, 'index'])
        ->name('trial_balance_report.index');

    // Route::get('/trial-balance-report', [\App\Http\Controllers\TrialBalanceReportController::class, 'index'])
    // ->name('trial.balance.report');

    // Route::get('/trial-balance-report/show', [\App\Http\Controllers\TrialBalanceReportController::class, 'show'])
    // ->name('trial.balance.report.show');
});

// *** Catch-all route HARUS PALING BAWAH ***
Route::get('/{any}', function () {
    return view('dashboard.index');
})->where('any', '.*');
