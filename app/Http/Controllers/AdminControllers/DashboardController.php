<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Book;
use App\Models\Category;
use App\Models\ReadingLog;
use App\Models\Feedback;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index() {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $last30 = $now->copy()->subDays(30);

        // Most read ebooks (top 5)
        $mostReadBooks = ReadingLog::selectRaw('book_id, COUNT(*) as read_count')
            ->groupBy('book_id')
            ->orderByDesc('read_count')
            ->with('book')
            ->take(5)
            ->get();

        // Most active users (top 5)
        $mostActiveUsers = ReadingLog::selectRaw('user_id, COUNT(*) as read_count')
            ->groupBy('user_id')
            ->orderByDesc('read_count')
            ->with('user')
            ->take(5)
            ->get();

        // Ebook uploads per month for the last 12 months
        $months = collect(range(0, 11))->map(function ($i) use ($now) {
            return $now->copy()->subMonths($i)->format('Y-m');
        })->reverse();
        $uploads = Book::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', $now->copy()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');
        $labels = $months->map(function ($m) { return Carbon::createFromFormat('Y-m', $m)->format('M Y'); })->values()->toArray();
        $data = $months->map(function ($m) use ($uploads) { return $uploads[$m] ?? 0; })->toArray();

        return Inertia::render('Admin/Dashboard', [
            'totalUsers' => User::count(),
            'totalBooks' => Book::count(),
            'newBooksThisMonth' => Book::where('created_at', '>=', $startOfMonth)->count(),
            'totalCategories' => Category::count(),
            'mostReadBooks' => $mostReadBooks,
            'mostActiveUsers' => $mostActiveUsers,
            'recentUsers' => User::orderByDesc('created_at')->take(5)->get(['id', 'user_name', 'email', 'created_at']),
            'recentBooks' => Book::orderByDesc('created_at')->take(5)->get(['id', 'title', 'author', 'created_at']),
            'activeReaders' => ReadingLog::where('read_at', '>=', $last30)->distinct('user_id')->count('user_id'),
            'feedbacks' => Feedback::count(),
            'ebookUploadsByMonth' => [
                'labels' => $labels,
                'data' => $data,
            ],
        ]);
    }
}
