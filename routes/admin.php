<?php

use App\Http\Controllers\AdminControllers\BookController;
use App\Http\Controllers\AdminControllers\CategoryController;
use App\Http\Controllers\AdminControllers\DashboardController;
use App\Http\Controllers\AdminControllers\ProfileController;
use App\Http\Controllers\AdminControllers\UserController;
use App\Http\Controllers\AdminControllers\AdminSettingsController;
use App\Http\Controllers\AdminControllers\AdminFeedbackController;
use App\Http\Controllers\AdminControllers\AdminTestimonialsController;
use App\Http\Controllers\AdminControllers\SearchController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth', 'admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Search Route
    Route::get('/admin/search', [SearchController::class, 'search'])->name('admin.search');

    //Admin Profile
    Route::controller(ProfileController::class)
        ->prefix('admin/profile')
        ->name('admin.profile.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/edit', 'edit')->name('edit');
            Route::put('/update', 'update')->name('update');
            Route::put('/password', 'updatePassword')->name('password.update');
            Route::delete('/delete', 'destroy')->name('destroy');
            Route::post('/send-verification-email', 'sendVerificationEmail')->name('sendVerificationEmail');
        });

    // Admin User Management Routes
    Route::controller(UserController::class)
        ->prefix('admin/users')
        ->name('admin.users.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::put('/update/{id}', 'update')->name('update');
            Route::delete('/delete/{id}', 'destroy')->name('destroy');
        });

    //Admin Books Management Routes
    Route::controller(BookController::class)
        ->prefix('admin/books')
        ->name('admin.books.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::patch('/toggle-status/{id}', 'toggleStatus')->name('toggle-status');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::put('/update/{id}', 'update')->name('update');
            Route::delete('/delete/{id}', 'destroy')->name('destroy');
            Route::get('/{id}', 'show')->name('show');
        });

    //Admin Category Management Routes
    Route::controller(CategoryController::class)
        ->prefix('admin/category')
        ->name('admin.categories.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/books/{id}', 'book')->name('books');
            Route::get('/edit/{id}', 'edit')->name('edit');
            Route::put('/update/{id}', 'update')->name('update');
            Route::delete('/delete/{id}', 'destroy')->name('destroy');
        });

    // Admin Settings Routes
    Route::controller(AdminSettingsController::class)
        ->prefix('admin/settings')
        ->name('admin.settings.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
        });

    // Admin Feedback Routes
    Route::controller(AdminFeedbackController::class)
        ->prefix('admin/feedback')
        ->name('admin.feedback.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/respond/{id}', 'respond')->name('respond');
            Route::put('/response/{id}', 'update')->name('update');
            Route::post('/mark-as-read', 'markAsRead')->name('mark-as-read');
        });
    
    Route::resource('testimonials', AdminTestimonialsController::class)->names('admin.testimonials');
});
