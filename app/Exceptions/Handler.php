<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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

        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*') || $request->is('v2/*')) {
                return response()->json(['message' => 'Item Not Found'], 404);
            }
        });

        $this->renderable(function (ValidationException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*') || $request->is('v2/*')) {
                return response()->json(['message' => 'Unprocessable Entity', 'errors' => [$e->getMessage()]], 422);
            }
        });
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->wantsJson() || $request->is('api/*') || $request->is('v2/*')) {
                return response()->json(['message' => 'The requested link does not exist'], 400);
            }
        });
        $this->renderable(function (AccessDeniedHttpException $e, $request) {
                return response()->json(['message' => $e->getMessage()], 403);
        });
    }
}
