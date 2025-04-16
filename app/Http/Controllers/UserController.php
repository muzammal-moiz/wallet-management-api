<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::with('wallet')->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'initial_balance' => $request->initial_balance,
            ]);

            $user->wallet()->create([
                'balance' => $request->initial_balance,
            ]);

            return new UserResource($user->load('wallet'));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return new UserResource($user->load('wallet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return new UserResource($user->load('wallet'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(null, 204);
    }
}