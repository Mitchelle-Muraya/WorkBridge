<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware; // ✅ Add this line

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register global or route-specific middleware
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'profile.complete' => \App\Http\Middleware\CheckProfileCompletion::class,
            'admin' => AdminMiddleware::class, // ✅ Add this line
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
