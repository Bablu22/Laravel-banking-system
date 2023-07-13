<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function getTransactionsAndBalance()
    {
        $user = Auth::user();
        $transactions = $user->transactions()->get();
        $balance = $user->balance;

        return response()->json(['transactions' => $transactions, 'balance' => $balance]);
    }

    public function getDeposits()
    {
        $user = Auth::user();
        $deposits = $user->transactions()->where('transaction_type', 'deposit')->get();

        return response()->json(['deposits' => $deposits]);
    }

    public function makeDeposit(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $user = User::findOrFail($validatedData['user_id']);
        // if not users does not found
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update user's balance
        $user->balance = $user->balance + $validatedData['amount'];
        $user->save();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'transaction_type' => 'deposit',
            'amount' => $validatedData['amount'],
            'fee' => 0,
            'date' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Deposit made successfully', 'transaction' => $transaction, 'balance' => $user->balance]);
    }

    public function getWithdrawals()
    {
        $user = Auth::user();
        $withdrawals = $user->transactions()->where('transaction_type', 'withdrawal')->get();

        return response()->json(['withdrawals' => $withdrawals]);
    }

    public function makeWithdrawal(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $user = User::findOrFail($validatedData['user_id']);
        // if not users does not found
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $accountType = $user->account_type;
        $withdrawalFee = $accountType === 'Individual' ? 0.015 : 0.025;

        // Apply withdrawal fee and handle free withdrawals
        $today = Carbon::now()->tz('Asia/Dhaka');
        $isFriday = $today->isFriday();
        $isFirst1KWithdrawal = $validatedData['amount'] <= 1000;
        $isFirst5KWithdrawal = $validatedData['amount'] <= 5000 && $user->transactions()
            ->where('transaction_type', 'withdrawal')
            ->whereMonth('date', $today->month)
            ->sum('amount') <= 5000;
        $totalWithdrawal = $user->transactions()
            ->where('transaction_type', 'withdrawal')
            ->sum('amount');
        $isBusinessAccountWith50KWithdrawal = $accountType === 'Business' && $totalWithdrawal >= 50000;

        $withdrawalFeeAmount = 0;

        if ($isFriday && !$isFirst1KWithdrawal && !$isFirst5KWithdrawal && !$isBusinessAccountWith50KWithdrawal) {
            $withdrawalFeeAmount = $validatedData['amount'] * $withdrawalFee;
            $validatedData['amount'] -= $withdrawalFeeAmount;
        }

        if ($validatedData['amount'] > $user->balance) {
            return response()->json(['message' => 'Insufficient balance']);
        }

        // Decrease user's balance
        $user->balance -= $validatedData['amount'];
        $user->save();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'transaction_type' => 'withdrawal',
            'amount' => $validatedData['amount'],
            'fee' => $withdrawalFeeAmount,
            'date' => Carbon::now(),
        ]);

        // Decrease withdrawal fee to 0.015% for Business accounts after 50K total withdrawal
        if ($accountType === 'Business' && $totalWithdrawal >= 50000) {
            $transaction->fee = $validatedData['amount'] * 0.015;
            $transaction->save();
        }

        return response()->json(['message' => 'Withdrawal made successfully', 'transaction' => $transaction]);
    }

}
