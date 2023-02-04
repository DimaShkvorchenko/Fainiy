<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
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
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (ValidationException $e) {
                return response()->json($e->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            })
            ->renderable(function (AuthenticationException $e) {
                return response()->json([
                    'message' => (!empty($e->getMessage()))? $e->getMessage() : 'Unauthorized'
                ], Response::HTTP_UNAUTHORIZED);
            })
            ->renderable(function (NotFoundHttpException $e) {
                return response()->json([
                    'message' => (!empty($e->getMessage()))? $e->getMessage() : 'Not found'
                ], Response::HTTP_NOT_FOUND);
            })
            ->renderable(function (Throwable $e) {
                return response()->json([
                    'message' => (!empty($e->getMessage()))? $e->getMessage() : 'Internal server error'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            });
    }
}
