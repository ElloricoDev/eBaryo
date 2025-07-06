<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Models\ReadingLog;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\User;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->where('status', 'active')->latest()->get();
        $user = Auth::user();
        $savedBookIds = $user ? $user->savedBooks()->pluck('book_id')->toArray() : [];
        return inertia('User/Books/Books', [
            'books' => $books,
            'saved_books' => $savedBookIds
        ]);
    }

    public function show($id)
    {
        $book = Book::with('category')->where('status', 'active')->findOrFail($id);
        $user = Auth::user();
        $savedBookIds = $user ? $user->savedBooks()->pluck('book_id')->toArray() : [];
        return inertia('User/Books/BookDetails', [
            'book' => $book,
            'saved_books' => $savedBookIds
        ]);
    }

    public function read($id)
    {
        $book = Book::with('category')->where('status', 'active')->findOrFail($id);
        $user = Auth::user();
        $lastPercent = null;
        if ($user) {
            $log = ReadingLog::where('user_id', $user->id)->where('book_id', $id)->first();
            if ($log) {
                $lastPercent = $log->last_percent;
            }
        }
        return inertia('User/Books/ReadBook', [
            'book' => $book,
            'lastPercent' => $lastPercent
        ]);
    }

    public function saveProgress(Request $request, $id)
    {
        $user = Auth::user();
        $percent = $request->input('percent');
        if (!$user) {
            abort(401);
        }
        $log = ReadingLog::firstOrNew([
            'user_id' => $user->id,
            'book_id' => $id,
        ]);
        $log->read_at = now();
        if (is_null($log->last_percent) || $percent > $log->last_percent) {
            $log->last_percent = $percent;
        }
        $log->save();
        return back();
    }

    public function saveBook(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }
        $book = Book::findOrFail($id);
        if (!$user->savedBooks()->where('book_id', $book->id)->exists()) {
            $user->savedBooks()->attach($book->id);
        }
        return back();
    }

    public function unsaveBook(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }
        $book = Book::findOrFail($id);
        $user->savedBooks()->detach($book->id);
        return back();
    }

    public function savedBooks()
    {
        $user = Auth::user();
        if (!$user) {
            abort(401, 'Unauthorized');
        }
        $books = $user->savedBooks()->with('category')->where('status', 'active')->get();
        $savedBookIds = $user->savedBooks()->where('status', 'active')->pluck('book_id')->toArray();
        return inertia('User/Books/Saved', [
            'books' => $books,
            'saved_books' => $savedBookIds
        ]);
    }
}
