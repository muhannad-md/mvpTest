<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Auth;

class UserController extends Controller
{
    public function __construct(){
        $this->middleware('can:update,user', ['only' => ['update', 'destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreUserRequest  $request
     * @return \Illuminate\Http\Response
     */
    // public function store(StoreUserRequest $request)
    // {
    //     $user = User::create([
    //         'username' => $request->username,
    //         'password' => Hash::make($request->password),
    //         'role' => $request->role,
    //         'deposit' => 0,
    //     ]);

    //     return new UserResource($user);
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User updated.',
            'user' => new UserResource($user)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        
        foreach($user->products as $product)
            $product->delete();

        $user->delete();
        return response()->json([
            'message' => 'User deleted.'
        ], 200);

    }
    
    public function deposit(Request $request)
    {
        $user = Auth::user();

        if($user->role != User::BYUER_ROLE)
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();

        $request->validate([
            'deposit' => ['required', 'integer', new \App\Rules\CostRule, 'in:5,10,20,50,100'],
        ]);

        $user->update([
            'deposit' => $user->deposit + $request->deposit,
        ]);

        return response()->json([
            'message' => 'Deposit Added.',
            'deposit' => $user->deposit,
        ], 200);
    }

    public function reset()
    {
        $user = Auth::user();

        if($user->role != User::BYUER_ROLE)
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        
        $user->update([
            'deposit' => 0,
        ]);

        return response()->json([
            'message' => 'Deposit Reset.'
        ], 200);
    }

}
