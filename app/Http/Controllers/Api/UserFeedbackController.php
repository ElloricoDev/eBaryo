<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Validator;

class UserFeedbackController extends Controller
{
    public function index(Request $request)
    {
        $feedback = Feedback::with(['book'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        return response()->json($feedback);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'type' => 'nullable|string',
            'book_id' => 'nullable|exists:books,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $feedback = Feedback::create([
            'user_id' => $request->user()->id,
            'message' => $request->message,
            'type' => $request->type,
            'book_id' => $request->book_id,
        ]);

        return response()->json($feedback, 201);
    }
} 