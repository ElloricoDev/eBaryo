<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\SocialiteController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\UserFeedbackController;
use App\Http\Controllers\Api\UserReadingLogController;
use App\Http\Controllers\Api\UserBookController;
use App\Http\Controllers\Api\UserProfileController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send']);
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify']);
    Route::get('/feedback', [UserFeedbackController::class, 'index']);
    Route::post('/feedback', [UserFeedbackController::class, 'store']);
    Route::get('/reading-log', [UserReadingLogController::class, 'index']);
    Route::get('/reading-log/{book_id}', [UserReadingLogController::class, 'show']);
    Route::post('/reading-log', [UserReadingLogController::class, 'store']);
    Route::get('/books', [UserBookController::class, 'index']);
    Route::get('/books/{id}', [UserBookController::class, 'show']);
    Route::post('/books/{id}/save', [UserBookController::class, 'save']);
    Route::delete('/books/{id}/save', [UserBookController::class, 'unsave']);
    Route::get('/books/saved', [UserBookController::class, 'saved']);
    Route::get('/profile', [UserProfileController::class, 'show']);
    Route::put('/profile', [UserProfileController::class, 'update']);
    Route::put('/profile/password', [UserProfileController::class, 'updatePassword']);
    Route::delete('/profile', [UserProfileController::class, 'destroy']);
});
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ForgotPasswordController::class, 'reset']);
Route::post('/socialite/{provider}', [SocialiteController::class, 'handleProviderCallback']);
