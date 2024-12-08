<?php

use App\Http\Middleware\CheckAdminRole;
use App\Http\Middleware\CheckUserRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'guest' => App\Http\Middleware\RedirectIfAuthenticated::class,
            'user' => CheckUserRole::class,
            'admin' => CheckAdminRole::class, // Add this line for admin middleware'
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
