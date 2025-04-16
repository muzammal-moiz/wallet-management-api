<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
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

    public function transfer(TransactionRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $senderWallet = Wallet::findOrFail($request->sender_wallet_id);
            $recipientWallet = User::findOrFail($request->recipient_id)->wallet;

            try {
                // Withdraw from sender
                $senderWallet->withdraw($request->amount);

                // Deposit to recipient
                $recipientWallet->deposit($request->amount);

                // Create transaction records
                $senderTransaction = Transaction::create([
                    'user_id' => $senderWallet->user_id,
                    'wallet_id' => $senderWallet->id,
                    'type' => 'transfer',
                    'amount' => $request->amount,
                    'description' => $request->description,
                    'recipient_id' => $request->recipient_id,
                ]);

                $recipientTransaction = Transaction::create([
                    'user_id' => $recipientWallet->user_id,
                    'wallet_id' => $recipientWallet->id,
                    'type' => 'transfer',
                    'amount' => $request->amount,
                    'description' => $request->description,
                    'recipient_id' => $request->recipient_id,
                ]);

                return response()->json([
                    'sender_wallet' => $senderWallet->fresh(),
                    'recipient_wallet' => $recipientWallet->fresh(),
                    'transactions' => [
                        'sender' => $senderTransaction,
                        'recipient' => $recipientTransaction,
                    ],
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 400);
            }
        });
    }

    public function userTransactions(User $user)
    {
        return Transaction::where('user_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
