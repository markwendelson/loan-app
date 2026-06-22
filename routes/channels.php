<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user-notification.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});