<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
// use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            if ($request->expectsJson()) {
                return response('Sorry, validation failed.', 422);
            }
        }
        
        if ($exception instanceof ThrottleException) {
            return response($exception->getMessage(), 429);
        }
        
        // if ($this->isHttpException($exception)) {
        //     if (request()->expectsJson()) {
        //         switch ($exception->getStatusCode()) {
        //             case 404:
        //                 return response()->json(['message' => 'Invalid request or url.'], 404);
        //                 break;
        //             case '500':
        //                 return response()->json(['message' => 'Server error. Please contact admin.'], 500);
        //                 break;

        //             default:
        //                 return $this->renderHttpException($exception);
        //                 break;
        //         }
        //     }
        // } else if ($exception instanceof ModelNotFoundException) {
        //     if (request()->expectsJson()) {
        //         return response()->json(['message' =>$exception->getMessage()], 404);
        //     }
        // } {
        //     return parent::render($request, $exception);
        // }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['message' => $exception->getMessage()], 401);
        }

        return redirect()->guest(route('login'));
    }
}
