<?php

namespace App\Http\Controllers;

use App\Models\BookReview;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookReviewController extends Controller
{
    // List reviews for a book
    public function index($bookId)
    {
        $book = Book::with('reviews.user')->findOrFail($bookId);
        return response()->json([
            'reviews' => $book->reviews()->with('user')->latest()->get(),
            'average_rating' => round($book->reviews()->avg('rating'), 2),
            'review_count' => $book->reviews()->count(),
        ]);
    }

    // Store a new review
    public function store(Request $request, $bookId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:2000',
        ]);
        $book = Book::findOrFail($bookId);
        $review = BookReview::create([
            'book_id' => $book->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'review' => $request->review,
        ]);
        return response()->json(['message' => 'Review submitted!', 'review' => $review->load('user')]);
    }

    // Optionally: Delete a review
    public function destroy($id)
    {
        $review = BookReview::findOrFail($id);
        if ($review->user_id !== Auth::id()) {
            abort(403);
        }
        $review->delete();
        return response()->json(['message' => 'Review deleted.']);
    }
}
