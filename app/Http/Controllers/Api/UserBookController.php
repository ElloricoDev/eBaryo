<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class UserBookController extends Controller
{
    public function index(Request $request)
    {
        $books = Book::with('category')->orderBy('title')->get();
        return response()->json($books);
    }

    public function show(Request $request, $id)
    {
        $book = Book::with('category')->find($id);
        if (!$book) {
            return response()->json(['message' => 'Book not found.'], 404);
        }
        return response()->json($book);
    }

    public function save(Request $request, $id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['message' => 'Book not found.'], 404);
        }
        $request->user()->savers()->syncWithoutDetaching([$id]);
        return response()->json(['message' => 'Book saved.']);
    }

    public function unsave(Request $request, $id)
    {
        $book = Book::find($id);
        if (!$book) {
            return response()->json(['message' => 'Book not found.'], 404);
        }
        $request->user()->savers()->detach($id);
        return response()->json(['message' => 'Book unsaved.']);
    }

    public function saved(Request $request)
    {
        $books = $request->user()->savers()->with('category')->get();
        return response()->json($books);
    }
} 