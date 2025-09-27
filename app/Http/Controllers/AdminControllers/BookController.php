<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::with('category');

        // Filter by status if provided
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $books = $query->paginate(15);

        // Attach read_count, reviews_count, and average_rating to each book
        $books->getCollection()->transform(function ($book) {
            $book->read_count = $book->readingLogs()->count();
            $book->reviews_count = $book->reviews()->count();
            $book->average_rating = round($book->reviews()->avg('rating'), 2);
            return $book;
        });

        return inertia('Admin/Books/Index', [
            'books' => $books,
            'filters' => $request->only(['status'])
        ]);
    }

    public function create()
    {
        $categories = Category::all();
        return inertia('Admin/Books/Create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_year' => 'nullable|integer',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ebook_file' => 'required|file|max:819200',
            'category_id' => 'required|exists:categories,id',
            'publisher' => 'nullable|string',
            'language' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = '/storage/' . $coverPath;
        }
        if ($request->hasFile('ebook_file')) {
            $sanitizedTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->title);
            $filename = $sanitizedTitle . '_' . time() . '.' . $request->file('ebook_file')->getClientOriginalExtension();
            $ebookPath = $request->file('ebook_file')->storeAs('ebooks', $filename, 'public');
            $validated['ebook_file'] = '/storage/' . $ebookPath;
        }

        Book::create($validated);
        return redirect()->route('admin.books.index')->with('message', 'Book created successfully.');
    }

    public function show($id)
    {
        $book = Book::with('category')->findOrFail($id);
        $book->read_count = $book->readingLogs()->count();
        $book->reviews_count = $book->reviews()->count();
        $book->average_rating = round($book->reviews()->avg('rating'), 2);
        return inertia('Admin/Books/View', [
            'book' => $book
        ]);
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);
        $categories = Category::all();
        return inertia('Admin/Books/Edit', [
            'book' => $book,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_year' => 'nullable|integer',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ebook_file' => 'nullable|file|max:819200',
            'category_id' => 'required|exists:categories,id',
            'publisher' => 'nullable|string',
            'language' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($book->cover_image && file_exists(public_path(str_replace('/storage/', 'storage/', $book->cover_image)))) {
                @unlink(public_path(str_replace('/storage/', 'storage/', $book->cover_image)));
            }
            $coverPath = $request->file('cover_image')->store('covers', 'public');
            $validated['cover_image'] = '/storage/' . $coverPath;
        } else {
            unset($validated['cover_image']);
        }
        if ($request->hasFile('ebook_file')) {
            if ($book->ebook_file && file_exists(public_path(str_replace('/storage/', 'storage/', $book->ebook_file)))) {
                @unlink(public_path(str_replace('/storage/', 'storage/', $book->ebook_file)));
            }
            $sanitizedTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->title);
            $filename = $sanitizedTitle . '_' . time() . '.' . $request->file('ebook_file')->getClientOriginalExtension();
            $ebookPath = $request->file('ebook_file')->storeAs('ebooks', $filename, 'public');
            $validated['ebook_file'] = '/storage/' . $ebookPath;
        } else {
            unset($validated['ebook_file']);
        }
        $book->update($validated);
        return redirect()->route('admin.books.index')->with('message', 'Book updated successfully.');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        $book->delete();
        return redirect()->route('admin.books.index')->with('message', 'Book deleted successfully.');
    }

    public function toggleStatus($id)
    {
        $book = Book::findOrFail($id);
        $book->status = $book->status === 'active' ? 'inactive' : 'active';
        $book->save();

        return back()->with('message', 'Book status updated successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'book_ids' => 'required|array',
            'book_ids.*' => 'exists:books,id'
        ]);

        Book::whereIn('id', $validated['book_ids'])->delete();

        return redirect()->back()->with('success', 'Selected books have been deleted successfully.');
    }
}
