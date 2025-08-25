<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Channel for user-specific feedback notifications
Broadcast::channel('user-feedback.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Channel for admin notifications (only admins can access)
Broadcast::channel('admin-notifications', function ($user) {
    return $user && $user->role === 'admin';
});
