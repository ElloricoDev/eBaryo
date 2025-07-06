<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Inertia\Inertia;

class AdminFeedbackController extends Controller
{
    public function index()
    {
        $feedbacks = Feedback::with('user')->orderByDesc('created_at')->get();
        return Inertia::render('Admin/Feedback', [
            'feedbacks' => $feedbacks
        ]);
    }

    public function respond($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->status = 'responded';
        $feedback->responded_at = now();
        $feedback->save();
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
        return back()->with('success', 'Response saved.');
    }
}
