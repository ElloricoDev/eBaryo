<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return inertia('Auth/Register');
    }

    public function register(Request $request){
        $validate = $request->validate([
            'user_name' => 'required|string|min:8',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed'
        ], [
            'email.unique' => 'This email is already registered.'
        ]);

        $user = User::create([
            'user_name' => $validate['user_name'],
            'email' => $validate['email'],
            'password' => bcrypt($validate['password'])
        ]);

        auth()->login($user);
        return redirect()->route('home');
    }
}
