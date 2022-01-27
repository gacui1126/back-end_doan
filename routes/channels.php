<?php

use App\GroupChat;
use App\Message;
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

// Broadcast::channel('App.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('privateChat.{receiverId}', function ($user, $receiverId) {
    return auth('api')->check();
});

Broadcast::channel('joinChat', function ($user) {
    return $user;
});

// Broadcast::channel('chat.{user}', function ($user, GroupChat $group) {
//     return $group->hasUser($user->id);
// });

// Broadcast::channel('privateChat', function ($user) {
//     return auth('api')->check();
// });
