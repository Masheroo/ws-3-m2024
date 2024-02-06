<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
        $this->renderable(function (AccessDeniedException $exception){
            return response()->json([
                'message' => 'Forbidden for you'
            ], 403);
        });
        $this->renderable(function (UnauthorizedException $exception){
           return response()->json([
               'message' => 'Login failed.'
           ], 403);
        });

        $this->renderable(function (NotFoundHttpException $exception){
            return response()->json([
                'message' => 'Not found'
            ], 404);
        });

    }
}
