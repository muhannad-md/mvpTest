<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (\Exception $e, $request) {
            return $this->handleException($e, $request);
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function handleException(\Exception $exception, $request)
    {
        if($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Unauthenticated.'
            ], 401);
        }
        if($exception instanceof AccessDeniedHttpException) {
            return response()->json([
                'message' => 'Unauthorized.'
            ], 403);
        }
        if($exception instanceof NotFoundHttpException || $exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'message' => 'Not found.'
            ], 404);
        }
    }
}
