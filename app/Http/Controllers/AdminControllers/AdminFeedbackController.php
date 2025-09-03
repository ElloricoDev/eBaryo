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

    public function toggleApprove($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->approved = !$feedback->approved;
        $feedback->save();
        return back()->with('success', 'Approval status updated.');
    }

    public function updateType(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|in:general,testimonial,bug_report,feature_request,report',
        ]);
        $feedback = Feedback::findOrFail($id);
        $feedback->type = $request->type;
        $feedback->save();
        return back()->with('success', 'Feedback type updated.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'feedback_ids' => 'required|array',
            'feedback_ids.*' => 'exists:feedback,id'
        ]);

        Feedback::whereIn('id', $validated['feedback_ids'])->delete();

        return redirect()->back()->with('success', 'Selected feedback has been deleted successfully.');
    }
}
