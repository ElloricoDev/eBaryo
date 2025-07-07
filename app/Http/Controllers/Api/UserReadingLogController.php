<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ReadingLog;
use Illuminate\Support\Facades\Validator;

class UserReadingLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = ReadingLog::with('book')
            ->where('user_id', $request->user()->id)
            ->orderBy('updated_at', 'desc')
            ->get();
        return response()->json($logs);
    }

    public function show(Request $request, $book_id)
    {
        $log = ReadingLog::where('user_id', $request->user()->id)
            ->where('book_id', $book_id)
            ->first();
        if (!$log) {
            return response()->json(['message' => 'No reading log found.'], 404);
        }
        return response()->json($log);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id',
            'last_percent' => 'required|numeric|min:0|max:100',
            'read_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $log = ReadingLog::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'book_id' => $request->book_id,
            ],
            [
                'last_percent' => $request->last_percent,
                'read_at' => $request->read_at ?? now(),
            ]
        );

        return response()->json($log, 201);
    }
} 