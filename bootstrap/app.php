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
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Database\QueryException $e) {
            // Check for database connection issues in QueryException
            if ($e->getCode() == 2002 || str_contains($e->getMessage(), 'Connection refused') || str_contains($e->getMessage(), 'actively refused')) {
                return response()->view('errors.db_error', [], 500);
            }
        });

        $exceptions->render(function (\PDOException $e) {
            // Check for database connection issues in PDOException
            if ($e->getCode() == 2002 || str_contains($e->getMessage(), 'Connection refused') || str_contains($e->getMessage(), 'actively refused')) {
                return response()->view('errors.db_error', [], 500);
            }
        });

        // Optional: Catch all other exceptions if not in debug mode
        $exceptions->render(function (Throwable $e) {
            if (!config('app.debug')) {
                return response()->view('errors.500', [], 500);
            }
        });
    })->create();
