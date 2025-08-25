<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Inertia\Inertia;
use App\Events\FeedbackUpdated;

class AdminFeedbackController extends Controller
{
    public function index()
    {
        // Mark all unseen pending feedbacks as seen
        \App\Models\Feedback::where('status', 'pending')->where('admin_notified', false)->update(['admin_notified' => true]);

        $feedbacks = Feedback::with('user')->orderByDesc('created_at')->get();
        return Inertia::render('Admin/Feedback', [
            'feedbacks' => $feedbacks
        ]);
    }

    public function markAsRead()
    {
        // Mark all pending feedbacks as notified
        \App\Models\Feedback::where('status', 'pending')->where('admin_notified', false)->update(['admin_notified' => true]);
        
        return response()->json(['success' => true]);
    }

    public function respond($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->status = 'responded';
        $feedback->responded_at = now();
        $feedback->save();
        // Broadcast to user and admin
        event(new FeedbackUpdated($feedback->user_id, 'user'));
        event(new FeedbackUpdated(null, 'admin'));
        return back()->with('success', 'Feedback marked as responded.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string|max:2000',
        ]);
        $feedback = Feedback::findOrFail($id);
        $feedback->response = $request->response;
        $feedback->status = 'responded';
        $feedback->responded_at = now();
        $feedback->notified = false;
        $feedback->save();
        // Broadcast to user and admin
        event(new FeedbackUpdated($feedback->user_id, 'user'));
        event(new FeedbackUpdated(null, 'admin'));
        return back()->with('success', 'Response saved.');
    }
}
