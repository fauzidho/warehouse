<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if (! app()->bound('view')) {
                return response(
                    "<div style='font-family:sans-serif;padding:2rem;background:#18181b;color:#f4f4f5;min-height:100vh;'>" .
                    "<h2 style='color:#ef4444;'>Original Exception:</h2>" .
                    "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>" .
                    "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . ":" . $e->getLine() . "</p>" .
                    "<pre style='background:#09090b;padding:1rem;color:#a1a1aa;overflow:auto;font-size:0.85rem;border-radius:0.5rem;'>" . htmlspecialchars($e->getTraceAsString()) . "</pre>" .
                    "</div>",
                    500,
                    ['Content-Type' => 'text/html']
                );
            }
        });
    })->create();
