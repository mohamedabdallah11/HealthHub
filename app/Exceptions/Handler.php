<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
{       
    if ($exception instanceof ThrottleRequestsException) {
        return new JsonResponse([
            'message' => 'Too many requests, please slow down!',
            'retry_after' => $exception->getHeaders()['Retry-After'] ?? 60, 
        ], 429);
    }

    return parent::render($request, $exception);
}
}
