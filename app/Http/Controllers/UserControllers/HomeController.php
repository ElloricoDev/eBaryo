<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\ReadingLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $books = Book::where('status', 'active')->latest()->get();
        $user = Auth::user();
        $continueReading = null;
        $savedBookIds = $user ? $user->savedBooks()->pluck('book_id')->toArray() : [];
        if ($user) {
            $continueReading = ReadingLog::with('book')
                ->where('user_id', $user->id)
                ->where('last_percent', '>', 0)
                ->whereHas('book', function($query) {
                    $query->where('status', 'active');
                })
                ->orderByDesc('updated_at')
                ->first();
        }

        // New Books (latest 5)
        $newBooks = Book::where('status', 'active')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        // Hot Books (most read in last 7 days)
        $hotBookIds = ReadingLog::where('created_at', '>=', Carbon::now()->subDays(7))
            ->selectRaw('book_id, COUNT(*) as read_count')
            ->groupBy('book_id')
            ->orderByDesc('read_count')
            ->take(5)
            ->pluck('book_id');
        $hotBooks = Book::whereIn('id', $hotBookIds)->get();

        // Most Read Books (all time)
        $mostReadBookIds = ReadingLog::selectRaw('book_id, COUNT(*) as read_count')
            ->groupBy('book_id')
            ->orderByDesc('read_count')
            ->take(5)
            ->pluck('book_id');
        $mostReadBooks = Book::whereIn('id', $mostReadBookIds)->get();

        // Highest Rated Books (at least 1 review)
        $highestRatedBooks = Book::whereHas('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(5)
            ->get();

        // Attach average_rating and reviews_count to all book lists
        $attachRatings = function($books) {
            return $books->map(function($book) {
                $book->average_rating = round($book->reviews()->avg('rating'), 2);
                $book->reviews_count = $book->reviews()->count();
                return $book;
            });
        };
        $books = $attachRatings($books);
        $newBooks = $attachRatings($newBooks);
        $hotBooks = $attachRatings($hotBooks);
        $mostReadBooks = $attachRatings($mostReadBooks);
        $highestRatedBooks = $attachRatings($highestRatedBooks);

        return inertia('User/Home', [
            'books' => $books,
            'continueReading' => $continueReading,
            'saved_books' => $savedBookIds,
            'newBooks' => $newBooks,
            'hotBooks' => $hotBooks,
            'mostReadBooks' => $mostReadBooks,
            'highestRatedBooks' => $highestRatedBooks,
        ]);
    }
}
