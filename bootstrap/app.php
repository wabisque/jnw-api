<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function(ValidationException $exception, Request $request): JsonResponse {
            if($request->is('api/*')) {
                $body = [
                    'status' => 422,
                    'message' => $exception->getMessage()
                ];

                if(App::environment('local')) {
                    $body['trace'] = $exception->getTrace();
                }

                return response()->json($body, 422);
            }
        });
    })->create();
