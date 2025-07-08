<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;

class BookController extends Controller
{
    // All books (recommended)
    public function index()
    {
        $books = Book::with(['category', 'reviews', 'readingLogs'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($book) => $this->formatBook($book));

        return response()->json(['books' => $books]);
    }

    // Newest books
    public function new()
    {
        $books = Book::with(['category', 'reviews', 'readingLogs'])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn($book) => $this->formatBook($book));

        return response()->json(['books' => $books]);
    }

    // Hot books (most saved)
    public function hot()
    {
        $books = Book::withCount('savers')
            ->with(['category', 'reviews', 'readingLogs'])
            ->where('status', 'active')
            ->orderByDesc('savers_count')
            ->take(10)
            ->get()
            ->map(fn($book) => $this->formatBook($book));

        return response()->json(['books' => $books]);
    }

    // Most read books (by reading logs count)
    public function mostRead()
    {
        $books = Book::withCount('readingLogs')
            ->with(['category', 'reviews', 'readingLogs'])
            ->where('status', 'active')
            ->orderByDesc('reading_logs_count')
            ->take(10)
            ->get()
            ->map(fn($book) => $this->formatBook($book));

        return response()->json(['books' => $books]);
    }

    // Highest rated books
    public function highestRated()
    {
        $books = Book::with(['category', 'reviews', 'readingLogs'])
            ->where('status', 'active')
            ->get()
            ->sortByDesc(fn($book) => $book->reviews->avg('rating'))
            ->take(10)
            ->map(fn($book) => $this->formatBook($book))
            ->values();

        return response()->json(['books' => $books]);
    }

    // Helper to format book with ratings, review count, and reading logs count
    private function formatBook($book)
    {
        return [
            'id' => $book->id,
            'title' => $book->title,
            'author' => $book->author,
            'isbn' => $book->isbn,
            'published_year' => $book->published_year,
            'description' => $book->description,
            'cover_image' => $book->cover_image,
            'ebook_file' => $book->ebook_file,
            'category' => $book->category ? [
                'id' => $book->category->id,
                'name' => $book->category->name,
            ] : null,
            'publisher' => $book->publisher,
            'language' => $book->language,
            'status' => $book->status,
            'average_rating' => round($book->reviews->avg('rating') ?? 0, 2),
            'reviews_count' => $book->reviews->count(),
            'reading_logs_count' => $book->readingLogs->count(),
        ];
    }
}