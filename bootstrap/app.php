<?php

use App\Http\Middleware\CheckDeviceIP;
use App\Http\Middleware\SetBranchPermissions;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // $middleware->append(CheckDeviceIP::class);
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\SetBranchPermissions::class,
            \App\Http\Middleware\QueueWritesOnFailover::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
