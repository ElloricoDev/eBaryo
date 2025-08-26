<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('query', '');
        
        if (empty($query)) {
            return redirect()->route('dashboard');
        }

        $results = [];
        $totalResults = 0;

        // Search Users
        $users = User::where('user_name', 'LIKE', "%{$query}%")
            ->orWhere('first_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->orWhere('email', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();
        
        $results['users'] = $users;
        $totalResults += $users->count();

        // Search Books
        $books = Book::with('category')
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('author', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->orWhere('isbn', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();
        
        $results['books'] = $books;
        $totalResults += $books->count();

        // Search Categories
        $categories = Category::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();
        
        $results['categories'] = $categories;
        $totalResults += $categories->count();

        // Search Feedback
        $feedbacks = Feedback::with('user')
            ->where('message', 'LIKE', "%{$query}%")
            ->orWhere('response', 'LIKE', "%{$query}%")
            ->limit(10)
            ->get();
        
        $results['feedbacks'] = $feedbacks;
        $totalResults += $feedbacks->count();

        return Inertia::render('Admin/Search', [
            'query' => $query,
            'results' => $results,
            'totalResults' => $totalResults,
        ]);
    }
}
