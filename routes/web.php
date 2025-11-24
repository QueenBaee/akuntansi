<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrialBalanceController;
use App\Http\Controllers\CashflowController;
use App\Http\Controllers\CashBankJournalController;
use App\Http\Controllers\CashAccountController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\LedgerController;

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('login');
    })->name('login');
    
    Route::post('/login', function (\Illuminate\Http\Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
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

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        // User Management
        Route::resource('users', \App\Http\Controllers\Web\UserController::class);
        
        // Accounts (master data)
        Route::resource('accounts', \App\Http\Controllers\Web\AccountController::class);
        
        // User Accounts (master data)
        Route::resource('user-accounts', \App\Http\Controllers\Web\UserAccountController::class);
        
        // Cash Accounts
        Route::resource('cash-accounts', CashAccountController::class);
        
        // Bank Accounts
        Route::resource('bank-accounts', BankAccountController::class);
        
        // Ledgers
        Route::resource('ledgers', LedgerController::class);
        
        // Ledgers with type filter
        Route::get('ledgers-cash', [LedgerController::class, 'index'])->name('ledgers.cash');
        Route::get('ledgers-bank', [LedgerController::class, 'index'])->name('ledgers.bank');
    });
    
    // Cash Transactions
    Route::resource('cash-transactions', \App\Http\Controllers\Web\CashTransactionController::class);
    
    // Journals
    Route::get('journals/create', [\App\Http\Controllers\CashBankJournalController::class, 'create'])->name('journals.create');
    Route::post('journals', [\App\Http\Controllers\CashBankJournalController::class, 'store'])->name('journals.store');
    Route::put('journals/{id}', [\App\Http\Controllers\CashBankJournalController::class, 'update'])->name('journals.update');
    Route::get('journals/{id}/attachments', [\App\Http\Controllers\CashBankJournalController::class, 'getAttachments'])->name('journals.attachments');
    Route::delete('journals/{id}', [\App\Http\Controllers\CashBankJournalController::class, 'destroy'])->name('journals.destroy');
    Route::resource('journals', \App\Http\Controllers\Web\JournalController::class)->except(['create', 'store', 'destroy']);
    
    // Memorials
    Route::get('memorials/create', [\App\Http\Controllers\MemorialController::class, 'create'])->name('memorials.create');
    Route::post('memorials', [\App\Http\Controllers\MemorialController::class, 'store'])->name('memorials.store');
    Route::put('memorials/{id}', [\App\Http\Controllers\MemorialController::class, 'update'])->name('memorials.update');
    Route::get('memorials/{id}/attachments', [\App\Http\Controllers\MemorialController::class, 'getAttachments'])->name('memorials.attachments');
    Route::delete('memorials/{id}', [\App\Http\Controllers\MemorialController::class, 'destroy'])->name('memorials.destroy');
    Route::resource('memorials', \App\Http\Controllers\Web\MemorialController::class)->except(['create', 'store', 'destroy']);
    
    // Routes accessible by both admin and user
    Route::middleware('role:admin|user')->group(function () {
        // Additional protected routes can go here
    });

    // Trial Balance
    Route::resource('trial-balance', TrialBalanceController::class);

    // Cashflow 
    Route::resource('cashflow', CashflowController::class);
});

// Catch all route (HARUS PALING BAWAH)
Route::get('/{any?}', function () {
    return view('dashboard.index');
})->where('any', '.*');
