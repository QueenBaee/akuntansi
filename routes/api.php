<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\BankTransactionController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\DepreciationController;
use App\Http\Controllers\MaklonTransactionController;
use App\Http\Controllers\RentIncomeController;
use App\Http\Controllers\RentExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'me']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    
    // User management
    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/assign-role', [UserController::class, 'assignRole']);
    Route::delete('users/{user}/remove-role', [UserController::class, 'removeRole']);
    
    // Role and permission management
    Route::apiResource('roles', RoleController::class);
    Route::post('roles/{role}/assign-permission', [RoleController::class, 'assignPermission']);
    Route::delete('roles/{role}/remove-permission', [RoleController::class, 'removePermission']);
    
    // Chart of accounts
    Route::apiResource('accounts', AccountController::class);
    Route::get('accounts/{account}/balance', [AccountController::class, 'getBalance']);
    Route::get('accounts/by-type/{type}', [AccountController::class, 'getByType']);
    
    // Journal entries
    Route::apiResource('journals', JournalController::class);
    Route::post('journals/{journal}/post', [JournalController::class, 'post']);
    Route::delete('journals/{journal}/unpost', [JournalController::class, 'unpost']);
    
    // Cash transactions
    Route::apiResource('cash-transactions', CashTransactionController::class);
    Route::get('cash-transactions/summary/daily', [CashTransactionController::class, 'dailySummary']);
    Route::get('cash-transactions/summary/monthly', [CashTransactionController::class, 'monthlySummary']);
    
    // Bank transactions
    Route::apiResource('bank-transactions', BankTransactionController::class);
    Route::post('bank-transactions/import', [BankTransactionController::class, 'import']);
    Route::get('bank-transactions/reconciliation/{account}', [BankTransactionController::class, 'reconciliation']);
    
    // Fixed assets
    Route::apiResource('assets', AssetController::class);
    Route::get('assets/{asset}/depreciation-schedule', [AssetController::class, 'depreciationSchedule']);
    Route::post('assets/{asset}/dispose', [AssetController::class, 'dispose']);
    
    // Depreciation
    Route::apiResource('depreciations', DepreciationController::class)->only(['index', 'show']);
    Route::post('depreciations/process-monthly', [DepreciationController::class, 'processMonthly']);
    Route::get('depreciations/schedule/{year}/{month}', [DepreciationController::class, 'monthlySchedule']);
    
    // Maklon transactions
    Route::apiResource('maklon-transactions', MaklonTransactionController::class);
    Route::get('maklon-transactions/summary/customer', [MaklonTransactionController::class, 'customerSummary']);
    Route::get('maklon-transactions/summary/product', [MaklonTransactionController::class, 'productSummary']);
    
    // Rent income
    Route::apiResource('rent-incomes', RentIncomeController::class);
    Route::get('rent-incomes/{rentIncome}/schedule', [RentIncomeController::class, 'schedule']);
    Route::post('rent-incomes/process-monthly', [RentIncomeController::class, 'processMonthly']);
    
    // Rent expense
    Route::apiResource('rent-expenses', RentExpenseController::class);
    Route::get('rent-expenses/{rentExpense}/schedule', [RentExpenseController::class, 'schedule']);
    Route::post('rent-expenses/process-monthly', [RentExpenseController::class, 'processMonthly']);
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('trial-balance', [ReportController::class, 'trialBalance']);
        Route::get('income-statement', [ReportController::class, 'incomeStatement']);
        Route::get('balance-sheet', [ReportController::class, 'balanceSheet']);
        Route::get('cash-flow', [ReportController::class, 'cashFlow']);
        Route::get('general-ledger', [ReportController::class, 'generalLedger']);
        Route::get('aging-receivables', [ReportController::class, 'agingReceivables']);
        Route::get('aging-payables', [ReportController::class, 'agingPayables']);
    });
    
    // Dashboard
    Route::prefix('dashboard')->group(function () {
        Route::get('stats', [DashboardController::class, 'stats']);
        Route::get('recent-transactions', [DashboardController::class, 'recentTransactions']);
        Route::get('summary', [DashboardController::class, 'summary']);
        Route::get('cash-flow-chart', [DashboardController::class, 'cashFlowChart']);
        Route::get('revenue-chart', [DashboardController::class, 'revenueChart']);
        Route::get('expense-chart', [DashboardController::class, 'expenseChart']);
    });
    
    // Utility routes
    Route::prefix('utils')->group(function () {
        Route::get('cashflow-categories', function () {
            return response()->json([
                'data' => \App\Models\CashflowCategory::where('is_active', true)->get()
            ]);
        });
        
        Route::get('account-types', function () {
            return response()->json([
                'data' => [
                    'asset' => 'Aset',
                    'liability' => 'Kewajiban', 
                    'equity' => 'Ekuitas',
                    'revenue' => 'Pendapatan',
                    'expense' => 'Beban'
                ]
            ]);
        });
        
        Route::get('permissions', function () {
            return response()->json([
                'data' => \Spatie\Permission\Models\Permission::all()
            ]);
        });
    });
});