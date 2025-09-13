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
        $userId = Auth::id();

        $categories = Category::select('id', 'name')->get();

        $feedbacks = Feedback::where('user_id', $userId)
            ->latest()
            ->get();

        $hadNewResponses = Feedback::where('user_id', $userId)
            ->where('status', 'responded')
            ->where('notified', false)
            ->tap(function ($query) use ($userId) {
                if ($query->exists()) {
                    $query->update(['notified' => true]);
                    event(new FeedbackUpdated($userId, 'user'));
                }
            })
            ->exists();

        return Inertia::render('User/Feedback', compact(
            'categories',
            'feedbacks',
            'hadNewResponses'
        ));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:2000',
            'type'    => 'nullable|string|in:general,testimonial,bug_report,feature_request,report',
        ]);

        $feedback = Feedback::create([
            'user_id'        => auth()->id(),
            'message'        => $data['message'],
            'status'         => 'pending',
            'type'           => $data['type'] ?? 'general',
            'admin_notified' => false,
        ]);

        if ($feedback) {
            event(new FeedbackUpdated(null, 'admin'));
        }

        return redirect()
            ->route('feedback.create')
            ->with('success', 'Thank you for your feedback!');
    }


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
