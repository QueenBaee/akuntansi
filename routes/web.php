<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrialBalanceController;
use App\Http\Controllers\CashflowController;

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
    });
    
    // Routes accessible by both admin and user
    Route::middleware('role:admin|user')->group(function () {
        // Cash Transactions - temporarily disabled
        // Route::resource('cash-transactions', \App\Http\Controllers\Web\CashTransactionController::class);
        
        // Journals - temporarily disabled
        // Route::resource('journals', \App\Http\Controllers\Web\JournalController::class);
        
        // Ledgers
        Route::get('ledgers', [\App\Http\Controllers\LedgerController::class, 'show'])->name('ledgers.index');
        
        // Ledger API routes (web-based)
        Route::prefix('api/ledgers')->group(function () {
            Route::get('{accountId}', [\App\Http\Controllers\LedgerController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\LedgerController::class, 'store']);
            Route::put('{ledger}', [\App\Http\Controllers\LedgerController::class, 'update']);
            Route::delete('{ledger}', [\App\Http\Controllers\LedgerController::class, 'destroy']);
        });
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
