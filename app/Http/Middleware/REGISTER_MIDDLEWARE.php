<?php
// bootstrap/app.php (Laravel 11+) OR app/Http/Kernel.php (Laravel 10)

// ═══════════════════════════════════════════════════════════
// For LARAVEL 11+ — update bootstrap/app.php like this:
// ═══════════════════════════════════════════════════════════
/*
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
*/

// ═══════════════════════════════════════════════════════════
// For LARAVEL 10 — add to $routeMiddleware in Kernel.php:
// ═══════════════════════════════════════════════════════════
/*
protected $routeMiddleware = [
    // ... existing middleware ...
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
*/
