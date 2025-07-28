<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
  
    
    public function register(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|min:8',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ],[
            'email.unique' => 'This email is already registered.'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        
        $user = User::create($validated);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'user_name' => $user->user_name,
                'email' => $user->email,
            ],
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);

        if (!auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => [
                'id' => $user->id,
                'user_name' => $user->user_name,
                'email' => $user->email,
            ],
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
       $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
