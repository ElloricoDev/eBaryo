<?php

use App\Http\Controllers\UserControllers\HomeController;
use App\Http\Controllers\UserControllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\UserControllers\BookController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\BookReviewController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('welcome')->middleware('guest');

Route::middleware('auth', 'user')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    //Profile Routes
    Route::controller(ProfileController::class)
        ->prefix('user/profile')
        ->name('user.profile.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/edit', 'edit')->name('edit');
            Route::put('/update', 'update')->name('update');
            Route::put('/password', 'updatePassword')->name('password.update');
            Route::delete('/delete', 'destroy')->name('destroy');
            Route::post('/send-verification-email', 'sendVerificationEmail')->name('sendVerificationEmail');
        });

    //Book Routes
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/saved', [BookController::class, 'savedBooks'])->name('books.saved');
    Route::get('/books/{id}', [BookController::class, 'show'])->name('books.view');
    Route::post('/books/{id}/progress', [BookController::class, 'saveProgress'])->name('books.saveProgress');
    Route::post('/books/{id}/save', [BookController::class, 'saveBook'])->name('books.save');
    Route::post('/books/{id}/unsave', [BookController::class, 'unsaveBook'])->name('books.unsave');

    //File serving route for PDFs and other ebooks
    Route::get('/files/{filename}', function ($filename) {
        $path = storage_path('app/public/ebooks/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        $file = file_get_contents($path);
        $type = mime_content_type($path);

        return response($file, 200, [
            'Content-Type' => $type,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    })->name('files.serve')->where('filename', '.*');

    // Test route to check if files are accessible
    Route::get('/test-pdf/{filename}', function ($filename) {
        $path = storage_path('app/public/ebooks/' . $filename);

        if (!file_exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->json([
            'exists' => true,
            'size' => filesize($path),
            'type' => mime_content_type($path),
            'path' => $path
        ]);
    })->name('test.pdf')->where('filename', '.*');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Book Reviews
    Route::get('/books/{book}/reviews', [BookReviewController::class, 'index'])->name('books.reviews.index');
    Route::post('/books/{book}/reviews', [BookReviewController::class, 'store'])->name('books.reviews.store');
    Route::delete('/reviews/{id}', [BookReviewController::class, 'destroy'])->name('reviews.destroy');

    // Feedback Routes
    Route::get('/feedback', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/my-feedback', [FeedbackController::class, 'myFeedback'])->name('feedback.my');
    Route::post('/books/{bookId}/report', [FeedbackController::class, 'reportBook'])->name('books.report');

    // Book Reading
    Route::get('/books/{id}/read/epub', [BookController::class, 'epubReader'])->name('books.epubReader');
    Route::get('/books/{id}/read/pdf', [BookController::class, 'pdfReader'])->name('books.pdfReader');
    Route::get('/user/profile/genres', [\App\Http\Controllers\UserControllers\GenreController::class, 'edit'])->name('user.profile.genres.edit');
    Route::post('/user/profile/genres', [\App\Http\Controllers\UserControllers\GenreController::class, 'update'])->name('user.profile.genres.update');
});

// Genre selection routes should be accessible to unverified users
Route::middleware(['auth'])->group(function () {
    Route::get('/user/genres/select', [\App\Http\Controllers\UserControllers\GenreController::class, 'select'])->name('user.genres.select');
    Route::post('/user/genres/select', [\App\Http\Controllers\UserControllers\GenreController::class, 'store'])->name('user.genres.store');
    Route::post('/user/genres/skip', [\App\Http\Controllers\UserControllers\GenreController::class, 'skip'])->name('user.genres.skip');
});

// Email verification routes - standard Laravel approach
Route::get('/email/verify', function () {
    $user = Auth::user();

    // Redirect users to their respective profile pages where they can verify email
    if ($user->role === 'admin') {
        return redirect()->route('admin.profile.index')
            ->with('message', 'Please verify your email address using the form below.');
    } else {
        return redirect()->route('user.profile.index')
            ->with('message', 'Please verify your email address using the form below.');
    }
})->name('verification.notice')->middleware('auth');

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, '__invoke'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', function () {
    request()->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

require __DIR__ . '/auth.php';
require __DIR__ . '/admin.php';
