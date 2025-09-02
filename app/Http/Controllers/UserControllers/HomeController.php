<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookReview;
use App\Models\ReadingLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Redirect to genre selection if user is logged in and has no genres selected
        if ($user && $user->genres()->count() === 0 && !session('skipped_genre_selection') && request()->route()->getName() !== 'user.genres.select') {
            return redirect()->route('user.genres.select');
        }
        $books = Book::with('category')->where('status', 'active')->latest()->get();
        $continueReading = null;
        $continueReadingList = null;
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
            $continueReadingList = ReadingLog::with('book')
                ->where('user_id', $user->id)
                ->where('last_percent', '>', 0)
                ->where('last_percent', '<', 1)
                ->whereHas('book', function($query) {
                    $query->where('status', 'active');
                })
                ->orderByDesc('updated_at')
                ->get();
        }

        // New Books (all active books, ordered by latest)
        $newBooks = Book::with('category')->where('status', 'active')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderByDesc('created_at')
            ->get();

        // Hot Books removed

        // Most Read Books (all time)
        $mostReadBookCounts = ReadingLog::selectRaw('book_id, COUNT(*) as read_count')
            ->groupBy('book_id')
            ->pluck('read_count', 'book_id');
        $mostReadBookIds = $mostReadBookCounts->keys();
        $mostReadBooks = Book::with('category')->whereIn('id', $mostReadBookIds)->get();

        // Highest Rated Books (at least 1 review)
        $highestRatedBooks = Book::with('category')->whereHas('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->get();
        // Only keep books with the highest rating value
        $topRating = $highestRatedBooks->first() ? $highestRatedBooks->first()->reviews_avg_rating : null;
        if ($topRating !== null) {
            $highestRatedBooks = $highestRatedBooks->filter(function($book) use ($topRating) {
                return $book->reviews_avg_rating == $topRating;
            });
        }

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
        $mostReadBooks = $attachRatings($mostReadBooks);
        $highestRatedBooks = $attachRatings($highestRatedBooks);
        // Filter out books with no reviews (reviews_count == 0)
        $highestRatedBooks = $highestRatedBooks->filter(function($book) {
            return $book->reviews_count > 0;
        });

        // Smarter Recommended Books based on user interests
        $recommendedBooks = collect();
        if ($user && $user->genres()->count() > 0) {
            $genreIds = $user->genres()->pluck('categories.id')->toArray();
            $excludeBookIds = $user->readingLogs()->pluck('book_id')->merge($user->savedBooks()->pluck('book_id'))->unique()->toArray();
            $recommendedBooks = Book::with('category')->where('status', 'active')
                ->whereIn('category_id', $genreIds)
                ->whereNotNull('category_id')
                ->whereNotIn('id', $excludeBookIds)
                ->inRandomOrder()
                ->get();
            // Filter out any books whose category is not in the selected genres (paranoia check)
            $recommendedBooks = $recommendedBooks->filter(function($book) use ($genreIds) {
                return $book->category && in_array($book->category->id, $genreIds);
            });
        }
        // Fallback: latest active books
        if ($recommendedBooks->isEmpty()) {
            $recommendedBooks = Book::with('category')->where('status', 'active')
                ->orderByDesc('created_at')
                ->get();
        }
        $recommendedBooks = $attachRatings($recommendedBooks);

        // Attach read_count to each book in all lists (after all lists are defined)
        $attachReadCount = function($books) use ($mostReadBookCounts) {
            return $books->map(function($book) use ($mostReadBookCounts) {
                $book->read_count = $mostReadBookCounts[$book->id] ?? 0;
                return $book;
            });
        };
        $books = $attachReadCount($books);
        $newBooks = $attachReadCount($newBooks);
        $mostReadBooks = $attachReadCount($mostReadBooks);
        $highestRatedBooks = $attachReadCount($highestRatedBooks);
        $recommendedBooks = $attachReadCount($recommendedBooks);

        // Attach user reading progress to all book lists (after all lists are defined)
        $progressMap = [];
        if ($user) {
            $progressMap = ReadingLog::where('user_id', $user->id)
                ->pluck('last_percent', 'book_id')
                ->toArray();
        }
        $attachProgress = function($books) use ($progressMap) {
            return $books->map(function($book) use ($progressMap) {
                $book->progress = isset($progressMap[$book->id]) ? $progressMap[$book->id] : null;
                return $book;
            });
        };
        $books = $attachProgress($books);
        $newBooks = $attachProgress($newBooks);
        $mostReadBooks = $attachProgress($mostReadBooks);
        $highestRatedBooks = $attachProgress($highestRatedBooks);
        $recommendedBooks = $attachProgress($recommendedBooks);

        // Fetch all reading logs for the user (for booksRead stat)
        $readingLogs = [];
        if ($user) {
            $readingLogs = ReadingLog::where('user_id', $user->id)
                ->with('book')
                ->get();
        }

        // Fetch all reviews written by the user (for reviewsWritten stat)
        $reviews = [];
        if ($user) {
            $reviews = BookReview::where('user_id', $user->id)->get();
        }

        $categories = Category::all();

        return inertia('User/Home', [
            'books' => $books,
            'continueReading' => $continueReading,
            'continueReadingList' => $continueReadingList,
            'saved_books' => $savedBookIds,
            'newBooks' => $newBooks,
            'mostReadBooks' => $mostReadBooks,
            'highestRatedBooks' => $highestRatedBooks,
            'recommendedBooks' => $recommendedBooks,
            'reading_logs' => $readingLogs,
            'reviews' => $reviews,
            'categories' => $categories,
        ]);
    }
}
