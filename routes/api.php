<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User Routes
Route::apiResource('users', UserController::class);

// Wallet Routes
Route::prefix('wallets')->group(function () {
    Route::post('/{wallet}/deposit', [WalletController::class, 'deposit']);
    Route::post('/{wallet}/withdraw', [WalletController::class, 'withdraw']);
});

// Transaction Routes
Route::prefix('transactions')->group(function () {
    Route::post('/transfer', [TransactionController::class, 'transfer']);
    Route::get('/user/{user}', [TransactionController::class, 'userTransactions']);
}); 