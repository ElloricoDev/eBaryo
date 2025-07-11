<?php

use Illuminate\Support\Facades\Broadcast;

// User private feedback channel
Broadcast::channel('user-feedback.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Admin feedback channel (all admins)
Broadcast::channel('admin-feedback', function ($user) {
    return $user->role === 'admin';
}); 