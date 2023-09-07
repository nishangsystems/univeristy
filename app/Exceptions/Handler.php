<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Arr;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
    //     $this->reportable(function (Throwable $e) {
    //         //
    //     });

        $this->renderable(function (\Exception $e) {
            if ($e->getPrevious() instanceof \Illuminate\Session\TokenMismatchException) {
                return redirect()->route('login');
            };
            if ($e instanceof \GuzzleHttp\Exception\ServerException) {
                # code...
                return redirect(route('admin.home'));
            }
        });
        
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'status' => 401,
                'message' => 'Your session has expired. Please login.'
            ], 401);
        }

        if ($request->is('admin') || $request->is('admin/*')) {
            return redirect()->guest('admin/login');
        }
 
        return redirect()->guest(route('admin.login'));
    }
}
