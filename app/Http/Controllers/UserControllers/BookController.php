<?php

namespace App\Http\Controllers\UserControllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookReview;
use Illuminate\Http\Request;
use App\Models\ReadingLog;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use Illuminate\Support\Carbon;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->input('category');
        $q = trim((string) $request->input('q', ''));
        $user = Auth::user();

        // Saved books (nullsafe operator in PHP 8+)
        $savedBookIds = $user?->savedBooks()->pluck('book_id')->toArray() ?? [];

        // Base query with eager loads & aggregates
        $baseQuery = Book::query()
            ->with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews', 'readingLogs')
            ->active()
            ->when($categoryId, fn($qb) => $qb->where('category_id', $categoryId))
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($q2) use ($q) {
                    $q2->where('title', 'like', "%{$q}%")
                        ->orWhere('author', 'like', "%{$q}%")
                        ->orWhereHas('category', fn($qc) => $qc->where('name', 'like', "%{$q}%"));
                });
            });

        $books = $baseQuery->latest()->get();

        // New Books (last 30 days)
        $newBooks = (clone $baseQuery)
            ->where('created_at', '>=', now()->subDays(30))
            ->orderByDesc('created_at')
            ->get();

        // Most Read Books (all time) — order by readingLogs_count
        $mostReadBooks = (clone $baseQuery)
            ->orderByDesc('reading_logs_count')
            ->take(20) // limit for efficiency
            ->get();

        // Highest Rated Books (at least 1 review, take only top rating group)
        $highestRatedBooks = (clone $baseQuery)
            ->has('reviews')
            ->orderByDesc('reviews_avg_rating')
            ->get();

        $topRating = $highestRatedBooks->first()?->reviews_avg_rating;
        if ($topRating) {
            $highestRatedBooks = $highestRatedBooks->where('reviews_avg_rating', $topRating);
        }

        // Attach progress for logged-in user
        $progressMap = $user
            ? ReadingLog::where('user_id', $user->id)->pluck('last_percent', 'book_id')->toArray()
            : [];

        foreach ([$books, $newBooks, $mostReadBooks, $highestRatedBooks] as $list) {
            $list->each(function ($book) use ($progressMap) {
                $book->progress = $progressMap[$book->id] ?? null;
            });
        }

        return inertia('User/Books/Books', [
            'books'             => $books,
            'saved_books'       => $savedBookIds,
            'newBooks'          => $newBooks,
            'mostReadBooks'     => $mostReadBooks,
            'highestRatedBooks' => $highestRatedBooks,
            'categories'        => Category::all(),
            'selectedCategory'  => $categoryId ? Category::find($categoryId) : null,
            'q'                 => $q,
        ]);
    }


    public function show($id)
    {
        $user = Auth::user();

        // Fetch book with aggregates
        $book = Book::with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews', 'readingLogs')
            ->active()
            ->findOrFail($id);

        // Saved books (use nullsafe operator for cleaner code)
        $savedBookIds = $user?->savedBooks()->pluck('book_id')->toArray() ?? [];

        // Reading progress (pluck single value instead of fetching whole row)
        $progress = $user
            ? ReadingLog::where('user_id', $user->id)
            ->where('book_id', $id)
            ->value('last_percent')
            : null;

        $book->progress = $progress;

        return inertia('User/Books/BookDetails', [
            'book'        => $book,
            'saved_books' => $savedBookIds,
        ]);
    }


    public function epubReader($id)
    {
        $user = Auth::user();

        // Load book with category (using scopeActive if you add it in Book model)
        $book = Book::with('category')
            ->active()
            ->findOrFail($id);

        // Fetch only the needed column instead of whole row
        $lastPercent = $user
            ? ReadingLog::where('user_id', $user->id)
            ->where('book_id', $id)
            ->value('last_percent')
            : null;

        return inertia('User/Books/EpubReader', [
            'book'        => $book,
            'lastPercent' => $lastPercent,
            'url'         => $book->ebook_file,
        ]);
    }

    public function saveProgress(Request $request, $id)
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $percent = (int) $request->input('percent');

        $log = ReadingLog::firstOrNew([
            'user_id' => $user->id,
            'book_id' => $id,
        ]);

        $log->read_at = now();

        // Only update if higher than previous progress
        if (is_null($log->last_percent) || $percent > $log->last_percent) {
            $log->last_percent = $percent;
        }

        $log->save();

        return back(); // stays compatible with Inertia
    }


    public function saveBook(Request $request, $id)
    {
        $user = Auth::user();
        abort_unless($user, 401, 'Unauthorized');

        $book = Book::active()->findOrFail($id);

        // Avoid duplicates with syncWithoutDetaching
        $user->savedBooks()->syncWithoutDetaching([$book->id]);

        return back();
    }

    public function unsaveBook(Request $request, $id)
    {
        $user = Auth::user();
        abort_unless($user, 401, 'Unauthorized');

        $book = Book::active()->findOrFail($id);

        $user->savedBooks()->detach($book->id);

        return back();
    }

    public function savedBooks()
    {
        $user = Auth::user();
        abort_unless($user, 401, 'Unauthorized');

        // All saved & active books with category
        $books = $user->savedBooks()
            ->with('category')
            ->active()
            ->get();

        $savedBookIds = $books->pluck('id')->toArray();

        // Aggregate reviews (count + avg) in fewer queries
        $reviewStats = BookReview::whereIn('book_id', $books->pluck('id'))
            ->selectRaw('book_id, COUNT(*) as reviews_count, ROUND(AVG(rating), 2) as average_rating')
            ->groupBy('book_id')
            ->pluck('reviews_count', 'book_id')
            ->toArray();

        $avgRatings = BookReview::whereIn('book_id', $books->pluck('id'))
            ->selectRaw('book_id, ROUND(AVG(rating), 2) as average_rating')
            ->groupBy('book_id')
            ->pluck('average_rating', 'book_id')
            ->toArray();

        // Read counts
        $readCounts = ReadingLog::whereIn('book_id', $books->pluck('id'))
            ->selectRaw('book_id, COUNT(*) as count')
            ->groupBy('book_id')
            ->pluck('count', 'book_id');

        // Attach stats to each book
        $books->transform(function ($book) use ($reviewStats, $avgRatings, $readCounts) {
            $book->reviews_count = $reviewStats[$book->id] ?? 0;
            $book->average_rating = $avgRatings[$book->id] ?? null;
            $book->read_count = $readCounts[$book->id] ?? 0;
            return $book;
        });

        // Finished books: last_percent >= 1
        $finishedBookIds = ReadingLog::where('user_id', $user->id)
            ->where('last_percent', '>=', 1)
            ->pluck('book_id');

        $finishedBooks = Book::with('category')
            ->active()
            ->whereIn('id', $finishedBookIds)
            ->get();

        // Currently reading: last_percent between 0 and 1
        $currentlyReadingLogs = ReadingLog::with(['book' => fn($q) => $q->active()])
            ->where('user_id', $user->id)
            ->whereBetween('last_percent', [0.01, 0.99])
            ->whereNotIn('book_id', $finishedBookIds)
            ->orderByDesc('updated_at')
            ->get();

        $currentlyReadingBooks = $currentlyReadingLogs
            ->map(fn($log) => tap($log->book, function ($book) use ($log) {
                if ($book) {
                    $book->last_percent = $log->last_percent;
                    $book->progress = $log->last_percent;
                }
            }))
            ->filter()
            ->values();

        $categories = Category::all();

        return inertia('User/Books/Saved', [
            'books'                 => $books,
            'saved_books'           => $savedBookIds,
            'currentlyReadingBooks' => $currentlyReadingBooks,
            'finishedBooks'         => $finishedBooks,
            'categories'            => $categories,
        ]);
    }
}
