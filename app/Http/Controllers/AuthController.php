<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;

class AuthController extends Controller
{
    public function login(Request $request){
        $login = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if( !Auth::attempt($login) ){
            return response()->json([
                'message' => 'Invalid Credentials.'
            ], 400);
        }

        $has_previous_login = 0;
        $userToken = Auth::user()->token();
        $tokens = Auth::user()->tokens;
        foreach($tokens as $token) {
            if($userToken != $token && $token->revoked == 0)
                $has_previous_login = 1;
        }
        
        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response()->json([
            'user' => new UserResource(Auth::user()),
            'access_token' => $accessToken,
            'previous_login' => (string)$has_previous_login,
        ], 400);

    }

    public function register(StoreUserRequest $request){
        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'deposit' => 0,
        ]);
        
        $login = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if( !Auth::attempt($login) ){
            return response()->json([
                'message' => 'Invalid Credentials.'
            ], 400);
        }

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        return response()->json([
            'user' => new UserResource(Auth::user()),
            'access_token' => $accessToken,
        ], 400);

    }

    public function logout()
    {
        $token = Auth::user()->token();
        if($token){
            $token->revoke();
        }
        return response()->json([
            'message' => 'Logged out.',
        ], 200);
    }

    public function logoutAll()
    {
        $userToken = Auth::user()->token();
        $tokens = Auth::user()->tokens;
        foreach($tokens as $token) {
            if($userToken != $token)
                $token->revoke();   
        }
        return response()->json([
            'message' => 'Logged out from all devices.',
        ], 200);
    }
}
