<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return Inertia::render('Admin/Profile/Index', [
            'user' => $user,
            'student' => $user->student,
        ]);
    }

    public function edit()
    {
        $user = Auth::user();

        return Inertia::render('Admin/Profile/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'user_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'student' => 'required|in:yes,no',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = '/storage/' . $avatarPath;
        }

        $user->update($validated);

        return redirect()->route('admin.profile.index')->with('message', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.profile.index')->with('message', 'Password updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return redirect('/')->with('message', 'Account deleted successfully.');
    }

    public function sendVerificationEmail(Request $request)
    {
        $user = Auth::user();
        $isGoogleUser = $user->google_id !== null;

        $rules = [];
        if (!$isGoogleUser) {
            $rules['password'] = 'required';
        }
        $request->validate($rules);

        if (!$isGoogleUser && !\Hash::check($request->password, $user->password)) {
            return redirect()->back()->withErrors(['password' => 'Incorrect password.']);
        }
        if ($user->hasVerifiedEmail()) {
            return redirect()->back()->withErrors(['message' => 'Email already verified.']);
        }
        $user->sendEmailVerificationNotification();
        return redirect()->back()->with('message', 'Verification email sent!');
    }
}