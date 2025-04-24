<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use App\Listeners\LogLastLogin;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            LogLastLogin::class,
        ]
    ];
}
