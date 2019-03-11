<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
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
    public function render($request, Exception $exception)
    {
        if($exception instanceof NotFoundHttpException)
        {
            return response()->json([
                'error_type' => 'NotFoundHttpException',
                'error' => 'La ruta a la que intenta acceder no existe',
                'code' => $exception->getStatusCode()
            ],$exception->getStatusCode());
        }

        if($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'error_type' => 'MethodNotAllowedHttpException',
                'error' => 'El metodo de acceso no esta permitido',
                'code' => $exception->getStatusCode()
            ], $exception->getStatusCode());
        }

        if($exception instanceof ValidationException) {
            return response()->json([
                'error_type' => 'ValidationException',
                'error' => $exception->errors()
            ]);
        }

        if($exception instanceof ModelNotFoundException) {
            return response()->json([
                'error_type' => 'ModelNotFoundException',
                'error' => "No se encontraron resultados para el filtro aplicado",
                'model' => str_replace("App\\","",$exception->getModel()),
            ]);
        }

        return response()->json([
            'error' => $exception->getMessage()
        ]);

        //return parent::render($request, $exception);
    }
}
