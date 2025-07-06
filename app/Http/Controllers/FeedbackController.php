<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function create()
    {
        return Inertia::render('User/Feedback');
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);
        Feedback::create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'status' => 'pending',
        ]);
        return redirect()->route('feedback.create')->with('success', 'Thank you for your feedback!');
    }

    public function myFeedback()
    {
        $feedbacks = Feedback::where('user_id', auth()->id())->orderByDesc('created_at')->get();
        $newResponses = $feedbacks->where('status', 'responded')->where('notified', false);
        // Mark as notified
        Feedback::whereIn('id', $newResponses->pluck('id'))->update(['notified' => true]);
        return Inertia::render('User/MyFeedback', [
            'feedbacks' => $feedbacks,
            'hasNewResponses' => $newResponses->count() > 0,
        ]);
    }

    public function reportBook(Request $request, $bookId)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
        ]);

        Feedback::create([
            'user_id' => auth()->id(),
            'message' => "Book Report - Book ID: {$bookId}\nReason: {$request->reason}\nDescription: " . ($request->description ?? 'No additional description'),
            'status' => 'pending',
            'type' => 'book_report',
            'book_id' => $bookId,
        ]);

        return back()->with('success', 'Book report submitted successfully. Thank you for helping us maintain quality content.');
    }
}
