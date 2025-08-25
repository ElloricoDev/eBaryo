<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use Inertia\Inertia;
use App\Events\FeedbackUpdated;
use App\Models\Category;

class FeedbackController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return Inertia::render('User/Feedback',[
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);
        $feedback = Feedback::create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'status' => 'pending',
            'admin_notified' => false,
        ]);
        // Broadcast to admin
        event(new FeedbackUpdated(null, 'admin'));
        return redirect()->route('feedback.create')->with('success', 'Thank you for your feedback!');
    }

    public function myFeedback()
    {
        $feedbacks = Feedback::where('user_id', auth()->id())->orderByDesc('created_at')->get();
        $newResponses = $feedbacks->where('status', 'responded')->where('notified', false);
        // Mark as notified
        Feedback::whereIn('id', $newResponses->pluck('id'))->update(['notified' => true]);
        // Broadcast to user (in case of real-time update)
        if ($newResponses->count() > 0) {
            event(new FeedbackUpdated(auth()->id(), 'user'));
        }

        $categories = Category::all();
        return Inertia::render('User/MyFeedback', [
            'feedbacks' => $feedbacks,
            'hasNewResponses' => $newResponses->count() > 0,
            'categories' => $categories,
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
            'admin_notified' => false,
        ]);

        return back()->with('success', 'Book report submitted successfully. Thank you for helping us maintain quality content.');
    }
}
