<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use Inertia\Inertia;
use App\Events\FeedbackUpdated;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        $userId = Auth::id();

        // Fetch user feedbacks (ordered) for display
        $feedbacks = Feedback::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        // Efficiently check and update any new responses without hydrating unnecessary models
        $newResponsesQuery = Feedback::where('user_id', $userId)
            ->where('status', 'responded')
            ->where('notified', false);

        $hadNewResponses = $newResponsesQuery->exists();

        if ($hadNewResponses) {
            // Mark as notified in bulk
            $newResponsesQuery->update(['notified' => true]);
            // Broadcast to user (in case of real-time update)
            event(new FeedbackUpdated($userId, 'user'));
        }

        return Inertia::render('User/Feedback', [
            'categories' => $categories,
            'feedbacks' => $feedbacks,
            'hasNewResponses' => $hadNewResponses,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'type' => 'nullable|string|in:general,testimonial,bug_report,feature_request,report',
        ]);
        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'status' => 'pending',
            'type' => $request->input('type', 'general'),
            'admin_notified' => false,
        ]);
        // Broadcast to admin
        event(new FeedbackUpdated(null, 'admin'));
        return redirect()->route('feedback.create')->with('success', 'Thank you for your feedback!');
    }

    // myFeedback() deprecated: listing merged into Feedback page

    public function reportBook(Request $request, $bookId)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        Feedback::create([
            'user_id' => Auth::id(),
            'message' => "Book Report - Book ID: {$bookId}\nReason: {$request->reason}\nDescription: " . ($request->description ?? 'No additional description'),
            'status' => 'pending',
            'type' => 'book_report',
            'book_id' => $bookId,
            'admin_notified' => false,
        ]);

        return back()->with('success', 'Book report submitted successfully. Thank you for helping us maintain quality content.');
    }
}
