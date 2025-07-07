<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $feedback = Feedback::all();
        return response()->json($feedback);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            // Add other feedback fields as needed
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $feedback = Feedback::create([
            'user_id' => $request->user()->id,
            'message' => $request->message,
            // Add other fields as needed
        ]);

        return response()->json($feedback, 201);
    }

    public function myFeedback(Request $request)
    {
        $feedback = Feedback::where('user_id', $request->user()->id)->get();
        return response()->json($feedback);
    }
} 