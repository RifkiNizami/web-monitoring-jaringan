<?php

use App\Http\Middleware\AuthenticateApiToken;
use App\Http\Middleware\AuthenticateSingleSession;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

        // Pasang Security Headers OWASP secara global
        $middleware->append(SecurityHeaders::class);

        // Single Device Login: verifikasi hash sesi di setiap request
        // (turunan \Illuminate\Session\Middleware\AuthenticateSession)
        $middleware->web(append: [
            AuthenticateSingleSession::class,
        ]);

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'api.token' => AuthenticateApiToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
