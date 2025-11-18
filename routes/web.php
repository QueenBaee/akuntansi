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
    return view('dashboard');
})->name('dashboard');

// Protected routes
Route::middleware('auth')->group(function () {

    // Accounts
    Route::resource('accounts', \App\Http\Controllers\Web\AccountController::class);
    
    // Cash Transactions
    Route::resource('cash-transactions', \App\Http\Controllers\Web\CashTransactionController::class);
    
    // Journals
    Route::resource('journals', \App\Http\Controllers\Web\JournalController::class);

    // Trial Balance
    Route::resource('trial-balance', TrialBalanceController::class);

    // Cashflow 
    Route::resource('cashflow', CashflowController::class);
});

// Catch all route (HARUS PALING BAWAH)
Route::get('/{any?}', function () {
    return view('dashboard');
})->where('any', '.*');
