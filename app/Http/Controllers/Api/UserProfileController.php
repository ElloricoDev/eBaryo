<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Mail;

class UserProfileController extends Controller
{
    // GET /api/user
    public function show(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }

    // PUT /api/user
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'user_name'      => ['string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'first_name'     => ['nullable', 'string', 'max:255'],
            'middle_name'    => ['nullable', 'string', 'max:255'],
            'last_name'      => ['nullable', 'string', 'max:255'],
            'contact_number' => ['nullable', 'string', 'max:255'],
            'address'        => ['nullable', 'string', 'max:255'],
            'student'        => ['nullable', Rule::in(['yes', 'no'])],
            'avatar'         => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return response()->json(['user' => $user]);
    }

    // PUT /api/user/password
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password'      => ['required'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['message' => 'Password updated successfully.']);
    }

    // POST /api/user/email/verify
    public function sendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        // Optionally require password confirmation
        if (!$user->google_id && !$user->facebook_id) {
            $request->validate(['password' => 'required']);
            if (!Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Password is incorrect.'], 422);
            }
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email sent.']);
    }

    // DELETE /api/user
    public function destroy(Request $request)
    {
        $user = $request->user();

        $request->validate(['password' => 'required']);
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Password is incorrect.'], 422);
        }

        // Optionally: delete avatar file
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return response()->json(['message' => 'Account deleted.']);
    }
}
