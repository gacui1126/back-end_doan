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

Broadcast::channel('notication.{userId}', function ($noti) {
    // return (int) auth('api')->user()->id != (int) $project->user_id;
    return auth('api')->check();
});

Broadcast::channel('taskdetail.{taskdetailId}', function ($taskdetail) {
    // return (int) auth('api')->user()->id != (int) $project->user_id;
    return auth('api')->check();
});
