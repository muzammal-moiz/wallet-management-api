<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\Transaction;
use App\Http\Requests\WalletRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function deposit(WalletRequest $request, Wallet $wallet)
    {
        return DB::transaction(function () use ($wallet, $request) {
            $wallet->deposit($request->amount);

            $transaction = Transaction::create([
                'user_id' => $wallet->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'deposit',
                'amount' => $request->amount,
                'description' => $request->description,
            ]);

            return response()->json([
                'wallet' => $wallet->fresh(),
                'transaction' => $transaction,
            ]);
        });
    }

    public function withdraw(WalletRequest $request, Wallet $wallet)
    {
        return DB::transaction(function () use ($wallet, $request) {
            try {
                $wallet->withdraw($request->amount);

                $transaction = Transaction::create([
                    'user_id' => $wallet->user_id,
                    'wallet_id' => $wallet->id,
                    'type' => 'withdrawal',
                    'amount' => $request->amount,
                    'description' => $request->description,
                ]);

                return response()->json([
                    'wallet' => $wallet->fresh(),
                    'transaction' => $transaction,
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 400);
            }
        });
    }
}
