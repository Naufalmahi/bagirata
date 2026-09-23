<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $this->renderable(function (BusinessException $e, Request $request) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'data' => null,
                    'message' => $e->getMessage(),
                    'errors' => null,
                ], $e->status);
            }

            return null;
        });
    }
}
