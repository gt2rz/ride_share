<?php

use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('drivers', function () {
    return true;
});

Broadcast::channel('passenger_{userId}', function ($userId) {
    return true;
});