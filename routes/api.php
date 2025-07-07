<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Authentication
Route::post('login', 'App\\Http\\Controllers\\Api\\AuthController@login');
Route::post('register', 'App\\Http\\Controllers\\Api\\AuthController@register');
Route::post('logout', 'App\\Http\\Controllers\\Api\\AuthController@logout')->middleware('auth:sanctum');
Route::get('user', 'App\\Http\\Controllers\\Api\\AuthController@user')->middleware('auth:sanctum');

// Books
Route::middleware('auth:sanctum')->group(function () {
    Route::get('books', 'App\\Http\\Controllers\\Api\\BookController@index');
    Route::get('books/{id}', 'App\\Http\\Controllers\\Api\\BookController@show');
    Route::post('books', 'App\\Http\\Controllers\\Api\\BookController@store');
    Route::put('books/{id}', 'App\\Http\\Controllers\\Api\\BookController@update');
    Route::delete('books/{id}', 'App\\Http\\Controllers\\Api\\BookController@destroy');

    // Categories
    Route::get('categories', 'App\\Http\\Controllers\\Api\\CategoryController@index');
    Route::get('categories/{id}', 'App\\Http\\Controllers\\Api\\CategoryController@show');

    // Feedback
    Route::get('feedback', 'App\\Http\\Controllers\\Api\\FeedbackController@index');
    Route::post('feedback', 'App\\Http\\Controllers\\Api\\FeedbackController@store');
    Route::get('feedback/my', 'App\\Http\\Controllers\\Api\\FeedbackController@myFeedback');

    // Profile
    Route::get('profile', 'App\\Http\\Controllers\\Api\\ProfileController@show');
    Route::put('profile', 'App\\Http\\Controllers\\Api\\ProfileController@update');
}); 