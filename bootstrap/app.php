<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // API talepleri için tüm istisnaları standart ServiceResponse formatına dönüştür.
        $exceptions->render(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return \App\Responses\ServiceResponse::error('Validation error', $e->errors(), 422)->toResponse();
                }
                if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
                    return \App\Responses\ServiceResponse::forbidden($e->getMessage())->toResponse();
                }
                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return \App\Responses\ServiceResponse::unauthorized($e->getMessage())->toResponse();
                }
                if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                    return \App\Responses\ServiceResponse::notFound('Resource not found.')->toResponse();
                }
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return \App\Responses\ServiceResponse::notFound('Endpoint not found.')->toResponse();
                }

                // Diğer tüm HTTP istisnaları için
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
                    return \App\Responses\ServiceResponse::error($e->getMessage(), null, $e->getStatusCode())->toResponse();
                }

                // Geliştirme ortamında detaylı hata mesajı, production'da genel mesaj ver
                $message = config('app.debug') ? $e->getMessage() : 'Internal Server Error';
                return \App\Responses\ServiceResponse::error($message, null, 500)->toResponse();
            }
        });
    })->create();
