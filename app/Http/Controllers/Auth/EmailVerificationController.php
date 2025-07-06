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
        $avatar = $user->avatar;
        if ($user->role === 'admin') {
            $redirect = redirect()->route('admin.profile.index');
        } else {
            $redirect = redirect()->route('user.profile.index');
        }
        if ($avatar) {
            $redirect->with(['avatar' => $avatar]);
        }
        return $redirect;
    }
} 