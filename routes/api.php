<?php

use App\Http\Controllers\AuthController;
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



Route::get('trial-balance/get-data', [App\Http\Controllers\TrialBalanceController::class, 'getData']);
Route::get('cashflow/get-data', [App\Http\Controllers\CashflowController::class, 'getData']);

// Batch Depreciation API
Route::post('batch-depreciation/preview', [App\Http\Controllers\BatchDepreciationController::class, 'preview']);
Route::post('batch-depreciation/process', [App\Http\Controllers\BatchDepreciationController::class, 'process']);

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