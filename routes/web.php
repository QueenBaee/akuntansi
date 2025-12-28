<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrialBalanceController;
use App\Http\Controllers\CashflowController;
use App\Http\Controllers\CashBankJournalController;
use App\Http\Controllers\CashAccountController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\BukuBesarController;
use App\Http\Controllers\TrialBalanceReportController;
use App\Http\Controllers\CashflowReportController;
use App\Http\Controllers\FinancialPositionController;
use App\Http\Controllers\ComprehensiveIncomeController;

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

        // Fixed Assets
        Route::get('fixed-assets', [\App\Http\Controllers\FixedAssetController::class, 'index'])->name('fixed-assets.index');
        Route::get('fixed-assets/create', [\App\Http\Controllers\FixedAssetController::class, 'create'])->name('fixed-assets.create');
        Route::get('fixed-assets/create-from-transaction', [\App\Http\Controllers\FixedAssetController::class, 'createFromTransaction'])->name('fixed-assets.create-from-transaction');
        Route::get('fixed-assets/merge-convert', [\App\Http\Controllers\FixedAssetController::class, 'showMergeConvert'])
            ->name('fixed-assets.show-merge-convert');
        Route::post('fixed-assets', [\App\Http\Controllers\FixedAssetController::class, 'store'])->name('fixed-assets.store');
        Route::post('fixed-assets/from-transaction', [\App\Http\Controllers\FixedAssetController::class, 'storeFromTransaction'])->name('fixed-assets.store-from-transaction');
        Route::post('fixed-assets/merge-convert', [\App\Http\Controllers\FixedAssetController::class, 'mergeConvert'])
            ->name('fixed-assets.merge-convert');
        Route::get('fixed-assets/{fixedAsset}', [\App\Http\Controllers\FixedAssetController::class, 'show'])->name('fixed-assets.show');
        Route::get('fixed-assets/{fixedAsset}/edit', [\App\Http\Controllers\FixedAssetController::class, 'edit'])->name('fixed-assets.edit');
        Route::put('fixed-assets/{fixedAsset}', [\App\Http\Controllers\FixedAssetController::class, 'update'])->name('fixed-assets.update');
        Route::delete('fixed-assets/{fixedAsset}', [\App\Http\Controllers\FixedAssetController::class, 'destroy'])->name('fixed-assets.destroy');
        Route::post('fixed-assets/{fixedAsset}/convert-to-regular', [\App\Http\Controllers\FixedAssetController::class, 'convertToRegular'])
            ->name('fixed-assets.convert-to-regular');
        Route::post('fixed-assets/{fixedAsset}/dispose', [\App\Http\Controllers\FixedAssetController::class, 'dispose'])
            ->name('fixed-assets.dispose');
        Route::post('fixed-assets/{fixedAsset}/mutations', [\App\Http\Controllers\FixedAssetController::class, 'storeMutation'])
            ->name('fixed-assets.mutations.store');
        
        // Assets in Progress
        Route::get('assets-in-progress', [\App\Http\Controllers\AssetInProgressController::class, 'index'])->name('assets-in-progress.index');
        Route::get('assets-in-progress/reclassify', [\App\Http\Controllers\AssetInProgressController::class, 'showReclassify'])
            ->name('assets-in-progress.reclassify');
        Route::post('assets-in-progress/reclassify', [\App\Http\Controllers\AssetInProgressController::class, 'reclassify'])
            ->name('assets-in-progress.reclassify.store');
        Route::get('assets-in-progress/{asset}', [\App\Http\Controllers\AssetInProgressController::class, 'show'])->name('assets-in-progress.show');
        
        // Fixed Asset API endpoints
        Route::get('api/fixed-assets/next-number', [\App\Http\Controllers\FixedAssetController::class, 'getNextAssetNumber'])
            ->name('api.fixed-assets.next-number');
        Route::get('api/fixed-assets/suggested-accounts', [\App\Http\Controllers\FixedAssetController::class, 'getSuggestedAccounts'])
            ->name('api.fixed-assets.suggested-accounts');

        // Asset Depreciation
        Route::post(
            'fixed-assets/{id}/depreciation/{period}/post',
            [\App\Http\Controllers\AssetDepreciationController::class, 'postMemorial']
        )
            ->name('fixed-assets.depreciation.post');
        Route::get('asset-depreciations', [\App\Http\Controllers\AssetDepreciationController::class, 'index'])
            ->name('asset-depreciations.index');

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
    Route::resource('journals', \App\Http\Controllers\Web\JournalController::class)->except(['create', 'store', 'destroy']);

    // Memorials - redirect to create like cash/bank
    Route::get('memorials', [\App\Http\Controllers\MemorialController::class, 'create'])->name('memorials.index');
    Route::get('memorials/create', [\App\Http\Controllers\MemorialController::class, 'create'])->name('memorials.create');
    Route::post('memorials', [\App\Http\Controllers\MemorialController::class, 'store'])->name('memorials.store');
    Route::put('memorials/{id}', [\App\Http\Controllers\MemorialController::class, 'update'])->name('memorials.update');
    Route::get('memorials/{id}/attachments', [\App\Http\Controllers\MemorialController::class, 'getAttachments'])->name('memorials.attachments');
    Route::get('memorials/{id}/attachments/{attachmentId}', [\App\Http\Controllers\MemorialController::class, 'viewAttachment'])->name('memorials.view-attachment');
    Route::delete('memorials/{id}', [\App\Http\Controllers\MemorialController::class, 'destroy'])->name('memorials.destroy');

    // Maklon
    Route::get('maklon', [\App\Http\Controllers\MaklonController::class, 'index'])->name('maklon.index');
    Route::post('maklon', [\App\Http\Controllers\MaklonController::class, 'store'])->name('maklon.store');
    Route::put('maklon/{id}', [\App\Http\Controllers\MaklonController::class, 'update'])->name('maklon.update');
    Route::delete('maklon/{id}', [\App\Http\Controllers\MaklonController::class, 'destroy'])->name('maklon.destroy');
    Route::post('maklon/{id}/post', [\App\Http\Controllers\MaklonController::class, 'post'])->name('maklon.post');
    Route::get('maklon/{id}/attachments/{attachmentId}', [\App\Http\Controllers\MaklonController::class, 'viewAttachment'])->name('maklon.view-attachment');

    // Trial Balance
    Route::resource('trial-balance', TrialBalanceController::class);

    // Cashflow
    Route::resource('cashflow', CashflowController::class);

    // Trial Balance Report
    Route::get('/trial-balance-report', [TrialBalanceReportController::class, 'index'])
        ->name('trial_balance_report.index');

    // Cashflow Report
    Route::get('/reports/cashflow', [CashflowReportController::class, 'index'])
        ->name('reports.cashflow');

    // General Ledger Report
    Route::get('/ledger', [LedgerController::class, 'index'])
        ->name('ledger.index');

    // Notes to Financial Statements Report
    Route::get('/notes-to-financial-statements', [\App\Http\Controllers\NotesToFinancialStatementsController::class, 'index'])
        ->name('notes-to-financial-statements.index');

    // Financial Position Report
    Route::get('/financial-position', [FinancialPositionController::class, 'index'])
        ->name('financial-position.index');

    // Comprehensive Income Report
    Route::get('/comprehensive-income', [ComprehensiveIncomeController::class, 'index'])
        ->name('comprehensive-income.index');
    // Buku Besar
    Route::get('/buku-besar', [BukuBesarController::class, 'index'])
        ->name('buku-besar.index');


    // Route::get('/trial-balance-report', [\App\Http\Controllers\TrialBalanceReportController::class, 'index'])
    // ->name('trial.balance.report');

    // Route::get('/trial-balance-report/show', [\App\Http\Controllers\TrialBalanceReportController::class, 'show'])
    // ->name('trial.balance.report.show');
});

// API Routes
Route::prefix('api')->group(function () {
    Route::get('accounts/search', [\App\Http\Controllers\Api\AccountSearchController::class, 'search'])->name('api.accounts.search');
    Route::get('cashflow/get-data', [CashflowController::class, 'getData'])->name('api.cashflow.get-data');
    Route::get('trial-balance/get-data', [TrialBalanceController::class, 'getData'])->name('api.trial-balance.get-data');
    Route::get('unconverted-asset-transactions', [\App\Http\Controllers\FixedAssetController::class, 'getUnconvertedTransactions'])->name('api.unconverted-asset-transactions');
});

// *** Catch-all route HARUS PALING BAWAH ***
Route::get('/{any}', function () {
    return view('dashboard.index');
})->where('any', '^(?!storage|css|js|images|assets|api).*');
