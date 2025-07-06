<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class SocialiteController extends Controller
{
    public function redirect () {
        // WAMP-specific SSL fix - disable SSL verification for development
        if (app()->environment('local')) {
            // Configure HTTP client to disable SSL verification
            Http::withOptions(['verify' => false]);
        }
        
        return Socialite::driver('google')->redirect();
    }

    public function callback (Request $request) {
        // WAMP-specific SSL fix - disable SSL verification for development
        if (app()->environment('local')) {
            // Configure HTTP client to disable SSL verification
            Http::withOptions(['verify' => false]);
        }
        
        try {
            // Get the authorization code from the request
            $code = $request->get('code');
            
            if (!$code) {
                throw new \Exception('Authorization code not received');
            }

            // Exchange code for access token using custom HTTP client
            $tokenResponse = Http::withOptions(['verify' => false])->post('https://www.googleapis.com/oauth2/v4/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => config('services.google.redirect'),
            ]);

            if (!$tokenResponse->successful()) {
                throw new \Exception('Failed to exchange code for token: ' . $tokenResponse->body());
            }

            $tokenData = $tokenResponse->json();
            $accessToken = $tokenData['access_token'];

            // Get user info using the access token
            $userResponse = Http::withOptions(['verify' => false])
                ->withHeaders(['Authorization' => 'Bearer ' . $accessToken])
                ->get('https://www.googleapis.com/oauth2/v2/userinfo');

            if (!$userResponse->successful()) {
                throw new \Exception('Failed to get user info: ' . $userResponse->body());
            }

            $googleUser = $userResponse->json();

            $fullName = $googleUser['name'] ?? '';
            $nameParts = explode(' ', $fullName, 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            $user = User::where('email', $googleUser['email'])->first();
            if ($user) {
                Auth::login($user);
                if ($user->role === 'admin') {
                    return redirect()->route('dashboard')->with('success', 'Admin login successful');
                } else {
                    return redirect()->route('home')->with('success', 'Login successful');
                }
            } else {
                $user = User::create([
                    'user_name' => $fullName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $googleUser['email'],
                    'google_id' => $googleUser['id'],
                    'password' => Hash::make(Str::random(10)),
                ]);
                Auth::login($user);
                return redirect()->route('home')->with('success', 'Login successful');
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Google OAuth Error: ' . $e->getMessage());
            
            // Return to login with error message
            return redirect()->route('login')->with('error', 'Google OAuth authentication failed. Please try again or use email login.');
        }
    }
}
