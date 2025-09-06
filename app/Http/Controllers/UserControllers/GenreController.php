<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class GenreController extends Controller
{
    public function select()
    {
        $genres = Category::all();
        $user = Auth::user();
        $selected = $user->genres()->pluck('categories.id')->toArray();
        return inertia('User/Genres/Select', [
            'genres' => $genres,
            'selected' => $selected,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $genreIds = $request->input('genres', []);
        $user->genres()->sync($genreIds);
        return redirect()->route('home')->with('success', 'Your interests have been saved!');
    }

    public function edit()
    {
        $genres = Category::all();
        $user = Auth::user();
        $selected = $user->genres()->pluck('categories.id')->toArray();
        return inertia('User/Profile/EditGenres', [
            'genres' => $genres,
            'selected' => $selected,
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $genreIds = $request->input('genres', []);
        $user->genres()->sync($genreIds);
        return redirect()->route('user.profile.index')->with('success', 'Your interests have been updated!');
    }

    public function skip(Request $request)
    {
        $request->session()->put('skipped_genre_selection', true);
        return redirect()->route('home');
    }
}
