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

        // Attach ratings and read count
        $bookIds = $books->pluck('id');
        $reviewCounts = \App\Models\BookReview::whereIn('book_id', $bookIds)
            ->selectRaw('book_id, COUNT(*) as count, AVG(rating) as avg_rating')
            ->groupBy('book_id')
            ->get()
            ->keyBy('book_id');
        $readCounts = \App\Models\ReadingLog::whereIn('book_id', $bookIds)
            ->selectRaw('book_id, COUNT(*) as count')
            ->groupBy('book_id')
            ->get()
            ->pluck('count', 'book_id');
        $books->transform(function($book) use ($reviewCounts, $readCounts) {
            $book->average_rating = isset($reviewCounts[$book->id]) ? round($reviewCounts[$book->id]->avg_rating, 2) : null;
            $book->reviews_count = isset($reviewCounts[$book->id]) ? $reviewCounts[$book->id]->count : 0;
            $book->read_count = isset($readCounts[$book->id]) ? $readCounts[$book->id] : 0;
            return $book;
        });

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
        // Add reading progress
        $progress = null;
        if ($user) {
            $log = \App\Models\ReadingLog::where('user_id', $user->id)->where('book_id', $id)->first();
            if ($log) {
                $progress = $log->last_percent;
            }
        }
        $book->progress = $progress;
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
        return inertia('User/Books/EpubReader', [
            'book' => $book,
            'lastPercent' => $lastPercent,
            'url' => $book->ebook_file,
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

        // Attach ratings and read count
        $bookIds = $books->pluck('id');
        $reviewCounts = \App\Models\BookReview::whereIn('book_id', $bookIds)
            ->selectRaw('book_id, COUNT(*) as count, AVG(rating) as avg_rating')
            ->groupBy('book_id')
            ->get()
            ->keyBy('book_id');
        $readCounts = \App\Models\ReadingLog::whereIn('book_id', $bookIds)
            ->selectRaw('book_id, COUNT(*) as count')
            ->groupBy('book_id')
            ->get()
            ->pluck('count', 'book_id');
        $books->transform(function($book) use ($reviewCounts, $readCounts) {
            $book->average_rating = isset($reviewCounts[$book->id]) ? round($reviewCounts[$book->id]->avg_rating, 2) : null;
            $book->reviews_count = isset($reviewCounts[$book->id]) ? $reviewCounts[$book->id]->count : 0;
            $book->read_count = isset($readCounts[$book->id]) ? $readCounts[$book->id] : 0;
            return $book;
        });

        return inertia('User/Books/Saved', [
            'books' => $books,
            'saved_books' => $savedBookIds
        ]);
    }
}
