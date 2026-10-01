<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: file_exists(__DIR__.'/../routes/api.php') ? __DIR__.'/../routes/api.php' : null,
        commands: file_exists(__DIR__.'/../routes/console.php') ? __DIR__.'/../routes/console.php' : null,
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'hr.admin' => \App\Http\Middleware\CheckHrOrAdmin::class,
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
     
            return redirect()->guest(route('welcome'));
        });
    })->create();
