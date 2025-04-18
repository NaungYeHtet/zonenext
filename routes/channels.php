<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.Admin.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
}, ['guards' => ['admin']]);
