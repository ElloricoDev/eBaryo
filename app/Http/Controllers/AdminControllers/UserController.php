<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(15);

        return inertia('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return inertia('Admin/Users/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:255|unique:users,user_name',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'in:user,admin',
        ]);

        // Optional: Set a default password
        $validated['password'] = Hash::make(Str::random(8));

        User::create($validated);

        return redirect()->route('admin.users.index');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return inertia('Admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'user_name' => 'required|string|max:255|unique:users,user_name,' . $id,
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'in:user,admin',
        ]);

        $user = User::findOrFail($id);
        $user->update($validated);

        return redirect()->route('admin.users.index');
    }

    public function show($id)
    {   
        $user = User::findOrFail($id);

        return inertia('Admin/Users/View', [
            'user' => $user,
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->back();
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id'
        ]);

        User::whereIn('id', $validated['user_ids'])->delete();

        return redirect()->back()->with('success', 'Selected users have been deleted successfully.');
    }
}
