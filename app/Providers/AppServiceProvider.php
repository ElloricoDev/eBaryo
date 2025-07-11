<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
        Schema::defaultStringLength(191);

        Inertia::share('newResponsesCount', function () {
            if (Auth::check()) {
                return \App\Models\Feedback::where('user_id', Auth::id())
                    ->where('status', 'responded')
                    ->where('notified', false)
                    ->count();
            }
            return 0;
        });
        // Share pending feedback count for admin
        Inertia::share('pendingFeedbackCount', function () {
            if (Auth::check() && Auth::user()->role === 'admin') {
                return \App\Models\Feedback::where('status', 'pending')->where('admin_notified', false)->count();
            }
            return 0;
        });
    }
}
