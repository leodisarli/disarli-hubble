<?php

namespace App\Exceptions;

use App\Exceptions\Custom\DataNotFoundException;
use App\Exceptions\Custom\DuplicateDataException;
use App\Exceptions\Custom\FilterException;
use App\Exceptions\Custom\ForbiddenException;
use App\Exceptions\Custom\InvalidCredentialsException;
use App\Exceptions\Custom\NotAuthorizedException;
use App\Exceptions\Custom\ValidationException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use JwtManager\ExpiredTokenException;
use JwtManager\InvalidTokenException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use ResponseJson\ResponseJson;
use Symfony\Component\HttpFoundation\Exception\SuspiciousOperationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    private $response;

    /**
     * A list of the exception types that should not be reported.
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        DataNotFoundException::class,
        DuplicateDataException::class,
        ExpiredTokenException::class,
        FilterException::class,
        ForbiddenException::class,
        HttpException::class,
        InvalidCredentialsException::class,
        InvalidTokenException::class,
        MethodNotAllowedHttpException::class,
        ModelNotFoundException::class,
        NotAuthorizedException::class,
        NotFoundHttpException::class,
        SuspiciousOperationException::class,
        ValidationException::class,
    ];

    public function __construct(
        ResponseJson $response
    ) {
        $this->response = $response;
    }

    /**
     * report or log an exception.
     * @param Exception $exception
     * @return void
     */
    public function report(
        Exception $exception
    ) {
        if ($this->shouldntReport($exception)) {
            return;
        }

        if (app()->bound('sentry') &&
            $this->shouldReport($exception) &&
            env("APP_ENV")=='production'
        ) {
            app('sentry')->captureException($exception);
        }
    
        parent::report($exception);
    }

    public function render(
        $request,
        Exception $exception
    ) {
        $requestId = $request->requestId ?? '';
        $startProfile = $request->startProfile ?? 0;
        if ($exception instanceof ValidationException) {
            $result = $this->response->response(
                $requestId,
                $startProfile,
                $request->jwtToken,
                $exception->getMessages(),
                'A validation error occurrs'
            );
            return response()->json($result, 422);
        }

        if ($exception instanceof HttpException) {
            $result = $this->response->response(
                $requestId,
                $startProfile,
                $request->jwtToken,
                [],
                'Route not found'
            );
            return response()->json($result, 404);
        }

        $code = ($exception->getCode()) ? : 500;
        if (!is_int($code) || $code > 505 || $code < 0) {
            $code = 500;
        }
        $message = $exception->getMessage();
        if ($code == 500) {
            if (ENV('APP_DEBUG') == 'true') {
                dd($exception);
            }
            $message = 'An unexpected error occurred, please try again later';
        }

        $result = $this->response->response(
            $requestId,
            $startProfile,
            $request->jwtToken,
            [],
            $message,
            $code
        );
        return response()->json($result, $code);
    }
}
