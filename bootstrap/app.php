<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //

        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        $middleware->alias([
            'permission' => \App\Http\Middleware\CheckHabilitation::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json([
                "success" => false,
                "message" => "Les données envoyées sont invalides - " . $e->getMessage() . ".",
                "errors" => $e->errors()
            ], 422);
        });

        $exceptions->render(function (InvalidArgumentException $e, $request) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        });

        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                "success" => false,
                "message" => "Non authentifié"
            ], 401);
        });

        $exceptions->render(function (AccessDeniedHttpException $e, $request) {
            return response()->json([
                "success" => false,
                "message" => "Accès non autorisé"
            ], 403);
        });

        $exceptions->render(function (Throwable $e, $request) {

            if (config('app.debug')) {
                return response()->json([
                    "success" => false,
                    "message" => $e->getMessage(),
                    "trace" => $e->getTrace(),
                ], 500);
            }

            return response()->json([
                "success" => false,
                "message" => "Erreur interne du serveur"
            ], 500);
        });
    })->create();
