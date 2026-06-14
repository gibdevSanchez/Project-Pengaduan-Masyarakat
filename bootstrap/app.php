<?php

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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->alias([
            'is.admin'        => \App\Http\Middleware\IsAdmin::class,
            'is.petugas'      => \App\Http\Middleware\IsPetugas::class,
            'is.masyarakat'   => \App\Http\Middleware\IsMasyarakat::class,
            'update.last.seen'=> \App\Http\Middleware\UpdateLastSeen::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $e): void {
            try {
                activity('exception')
                    ->withProperties([
                        'ip'      => request()->ip(),
                        'url'     => request()->fullUrl(),
                        'class'   => class_basename($e),
                        'file'    => str_replace(base_path(), '', $e->getFile()),
                        'line'    => $e->getLine(),
                        'message' => \Illuminate\Support\Str::limit($e->getMessage(), 200),
                    ])
                    ->log('[' . class_basename($e) . '] ' . \Illuminate\Support\Str::limit($e->getMessage(), 120));
            } catch (\Throwable) {
                // never let logging crash the app
            }
        });
    })->create();
