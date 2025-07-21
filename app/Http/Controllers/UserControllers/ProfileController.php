<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $categories = Category::all();
        $genres = $user->genres()->pluck('name')->toArray();
        return inertia('User/Profile/Index', [
            'user' => $user,
            'genres' => $genres,
            'categories' => $categories,
        ]);
    }

    public function edit()
    {
        return inertia('User/Profile/Edit', [
            'user' => Auth::user(),
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

        return redirect()->route('user.profile.index')->with('message', 'Profile updated successfully.');
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

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return redirect(route('welcome'))->with('message', 'Account deleted successfully.');
    }

    public function showCompleteForm()
    {
        return inertia('User/Profile/Complete');
    }

    public function complete(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->middle_name = $request->middle_name;
        $user->contact_number = $request->contact_number;
        $user->address = $request->address;
        $user->profile_completed = true;
        $user->save();

        return redirect()->route('dashboard')->with('message', 'Profile completed!');
    }
}
