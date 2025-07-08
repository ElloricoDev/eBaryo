<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\API\UserProfileController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::get('/books', [BookController::class, 'index']);
Route::get('/books/new', [BookController::class, 'new']);
Route::get('/books/hot', [BookController::class, 'hot']);
Route::get('/books/most-read', [BookController::class, 'mostRead']);
Route::get('/books/highest-rated', [BookController::class, 'highestRated']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    //profile
    Route::get('/user', [UserProfileController::class, 'show']);
    Route::put('/user', [UserProfileController::class, 'update']);
    Route::put('/user/password', [UserProfileController::class, 'updatePassword']);
    Route::post('/user/email/verify', [UserProfileController::class, 'sendVerificationEmail']);
    Route::delete('/user', [UserProfileController::class, 'destroy']);
});
