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
        $book = Book::findOrFail($bookId);

        $reviews = $book->reviews()
            ->with('user')
            ->latest()
            ->get();

        $averageRating = round($book->reviews()->avg('rating'), 2);
        $reviewCount = $book->reviews()->count();

        $userReview = Auth::check()
            ? $book->reviews()->where('user_id', Auth::id())->first()
            : null;

        return response()->json([
            'reviews'        => $reviews,
            'average_rating' => $averageRating,
            'review_count'   => $reviewCount,
            'user_review'    => $userReview,
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
        // Only one review per user per book
        $review = BookReview::updateOrCreate(
            [
                'book_id' => $book->id,
                'user_id' => Auth::id(),
            ],
            [
                'rating' => $request->rating,
                'review' => $request->review,
            ]
        );
        return back()->with('success', 'Review submitted!');
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
