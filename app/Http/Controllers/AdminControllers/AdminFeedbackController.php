<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Inertia\Inertia;
use App\Events\FeedbackUpdated;
use Illuminate\Support\Facades\DB;

class AdminFeedbackController extends Controller
{
    public function index()
    {
        // Mark all unseen pending feedbacks as seen (only if there are any)
        $hasUnseenFeedbacks = Feedback::where('status', 'pending')
            ->where('admin_notified', false)
            ->exists();
            
        if ($hasUnseenFeedbacks) {
            DB::transaction(function () {
                Feedback::where('status', 'pending')
                    ->where('admin_notified', false)
                    ->update(['admin_notified' => true]);
            });
        }

        $feedbacks = Feedback::with('user')->orderByDesc('created_at')->get();
        return Inertia::render('Admin/Feedback', [
            'feedbacks' => $feedbacks
        ]);
    }

    public function markAsRead()
    {
        // Mark all pending feedbacks as notified
        DB::transaction(function () {
            Feedback::where('status', 'pending')
                ->where('admin_notified', false)
                ->update(['admin_notified' => true]);
        });
        
        return response()->json(['success' => true]);
    }

    public function respond($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'status' => 'responded',
            'responded_at' => now()
        ]);
        
        // Broadcast to both user and admin
        $this->broadcastFeedbackUpdate($feedback->user_id);
        
        return back()->with('success', 'Feedback marked as responded.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'response' => 'required|string|max:2000',
        ]);
        
        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'response' => $request->response,
            'status' => 'responded',
            'responded_at' => now(),
            'notified' => false
        ]);
        
        // Broadcast to both user and admin
        $this->broadcastFeedbackUpdate($feedback->user_id);
        
        return back()->with('success', 'Response saved.');
    }

    public function toggleApprove($id)
    {
        Feedback::where('id', $id)->update([
            'approved' => DB::raw('NOT approved')
        ]);
        
        return back()->with('success', 'Approval status updated.');
    }

    public function updateType(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|in:general,testimonial,bug_report,feature_request,report',
        ]);
        
        Feedback::where('id', $id)->update([
            'type' => $request->type
        ]);
        
        return back()->with('success', 'Feedback type updated.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'feedback_ids' => 'required|array',
            'feedback_ids.*' => 'exists:feedback,id'
        ]);

        DB::transaction(function () use ($validated) {
            Feedback::whereIn('id', $validated['feedback_ids'])->delete();
        });

        return redirect()->back()->with('success', 'Selected feedback has been deleted successfully.');
    }

    /**
     * Broadcast feedback update to both user and admin
     */
    private function broadcastFeedbackUpdate($userId)
    {
        event(new FeedbackUpdated($userId, 'user'));
        event(new FeedbackUpdated(null, 'admin'));
    }
}
