<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

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

        if ($exception instanceof ModelNotFoundException) {
            $model = class_basename($exception->getModel());

            $message = match ($model) {
                'Category' => 'category_id does not exist.',
                'Product' => 'product_id does not exist.',
                'Order' => 'order_id does not exist.',
                default => 'Resource not found.'
            };

            return response()->json([
                'message' => $message
            ], 404);
        }

        return parent::render($request, $exception);
    }
}
