<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController
{
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        $request->fulfill();

        $user = Auth::user();
        
        // Redirect based on role
        if ($user->role === 'admin') {
            return redirect()->route('admin.profile.index')
                ->with('message', 'Email verified successfully!');
        } else {
            return redirect()->route('user.profile.index')
                ->with('message', 'Email verified successfully!');
        }
    }
} 