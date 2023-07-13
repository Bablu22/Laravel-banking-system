<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Registration adn  login routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/profile', [AuthController::class, 'getAuthenticatedUser']);

Route::middleware(['auth.jwt'])->group(function () {

    // Fetch Transactions and Balance
    Route::get('/transactions', [UserController::class, 'getTransactionsAndBalance']);

    // Fetch Deposits
    Route::get('/deposit', [UserController::class, 'getDeposits']);

    // Make a Deposit
    Route::post('/deposit', [UserController::class, 'makeDeposit']);

    // Fetch Withdrawals
    Route::get('/withdrawal', [UserController::class, 'getWithdrawals']);

    // Make a Withdrawal
    Route::post('/withdrawal', [UserController::class, 'makeWithdrawal']);

});
