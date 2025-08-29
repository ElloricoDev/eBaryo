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
        $savedBookIds = $user ? $user->savedBooks()->pluck('book_id')->toArray() : [];

        $baseQuery = Book::with('category')->where('status', 'active');
        if ($categoryId) {
            $baseQuery = $baseQuery->where('category_id', $categoryId);
        }
        if ($q !== '') {
            $baseQuery = $baseQuery->where(function($qb) use ($q) {
                $qb->where('title', 'like', "%{$q}%")
                   ->orWhere('author', 'like', "%{$q}%")
                   ->orWhereHas('category', function($qc) use ($q) {
                       $qc->where('name', 'like', "%{$q}%");
                   });
            });
        }
        $books = $baseQuery->latest()->get();

        // New Books (last 30 days)
        $newBooks = Book::with('category')->where('status', 'active')
            ->when($categoryId, fn($qb) => $qb->where('category_id', $categoryId))
            ->when($q !== '', function($qb) use ($q) {
                $qb->where(function($q2) use ($q) {
                    $q2->where('title', 'like', "%{$q}%")
                       ->orWhere('author', 'like', "%{$q}%")
                       ->orWhereHas('category', function($qc) use ($q) {
                           $qc->where('name', 'like', "%{$q}%");
                       });
                });
            })
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->orderByDesc('created_at')
            ->get();

        // Hot Books removed

        // Most Read Books (all time)
        $mostReadBookCounts = ReadingLog::when($categoryId, function($q) use ($categoryId) {
                $q->whereHas('book', function($b) use ($categoryId) {
                    $b->where('category_id', $categoryId);
                });
            })
            ->selectRaw('book_id, COUNT(*) as read_count')
            ->groupBy('book_id')
            ->pluck('read_count', 'book_id');
        $mostReadBookIds = $mostReadBookCounts->keys();
        $mostReadBooks = Book::with('category')->whereIn('id', $mostReadBookIds)
            ->when($categoryId, fn($qb) => $qb->where('category_id', $categoryId))
            ->when($q !== '', function($qb) use ($q) {
                $qb->where(function($q2) use ($q) {
                    $q2->where('title', 'like', "%{$q}%")
                       ->orWhere('author', 'like', "%{$q}%")
                       ->orWhereHas('category', function($qc) use ($q) {
                           $qc->where('name', 'like', "%{$q}%");
                       });
                });
            })
            ->get();

        // Highest Rated Books (at least 1 review)
        $highestRatedBooks = Book::with('category')->whereHas('reviews')
            ->when($categoryId, fn($qb) => $qb->where('category_id', $categoryId))
            ->when($q !== '', function($qb) use ($q) {
                $qb->where(function($q2) use ($q) {
                    $q2->where('title', 'like', "%{$q}%")
                       ->orWhere('author', 'like', "%{$q}%")
                       ->orWhereHas('category', function($qc) use ($q) {
                           $qc->where('name', 'like', "%{$q}%");
                       });
                });
            })
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->get();
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
        $highestRatedBooks = $highestRatedBooks->filter(function($book) {
            return $book->reviews_count > 0;
        });

        // Attach read_count to each book in all lists
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

        // Attach user reading progress to all book lists
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

        $categories = Category::all();
        $selectedCategory = $categoryId ? Category::find($categoryId) : null;

        return inertia('User/Books/Books', [
            'books' => $books,
            'saved_books' => $savedBookIds,
            'newBooks' => $newBooks,
            'mostReadBooks' => $mostReadBooks,
            'highestRatedBooks' => $highestRatedBooks,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'q' => $q,
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
            $log = ReadingLog::where('user_id', $user->id)->where('book_id', $id)->first();
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

    public function epubReader($id)
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

    public function pdfReader($id)
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
        return inertia('User/Books/PdfReader', [
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
        $reviewCounts = BookReview::whereIn('book_id', $bookIds)
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

        // Finished: last_percent >= 1
        $finishedBookIds = ReadingLog::where('user_id', $user->id)
            ->where('last_percent', '>=', 1)
            ->pluck('book_id');
        $finishedBooks = Book::with('category')
            ->whereIn('id', $finishedBookIds)
            ->where('status', 'active')
            ->get();

        // Currently Reading: last_percent > 0 and < 1, but exclude finished books
        $currentlyReadingLogs = ReadingLog::with('book')
            ->where('user_id', $user->id)
            ->where('last_percent', '>', 0)
            ->where('last_percent', '<', 1)
            ->whereNotIn('book_id', $finishedBookIds)
            ->whereHas('book', function($query) {
                $query->where('status', 'active');
            })
            ->orderByDesc('updated_at')
            ->get();
        $currentlyReadingBooks = $currentlyReadingLogs->map(function($log) {
            if ($log->book) {
                $book = $log->book;
                $book->last_percent = $log->last_percent;
                $book->progress = $log->last_percent;
                return $book;
            }
        })->filter()->values();

        $categories = Category::all();
        return inertia('User/Books/Saved', [
            'books' => $books,
            'saved_books' => $savedBookIds,
            'currentlyReadingBooks' => $currentlyReadingBooks,
            'finishedBooks' => $finishedBooks,
            'categories' => $categories,
        ]);
    }

    
}
