<?php

namespace App\Exceptions;

use Error;
use Exception;
use Throwable;
use LogicException;
use RedisException;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use infra\exceptions\HttpRequestFailed;
use infra\librarys\utils\Functions;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param Throwable $exception exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        $this->dontReport = [];
        parent::report($exception);
    }

    /**
     * Prepare a JSON response for the given exception.
     *
     * @param Request $request request
     * @param Throwable $e e
     * @return JsonResponse
     */
    protected function prepareJsonResponse($request, Throwable $e)
    {
        $code = $e->getCode() ?: -1;
        $message = $e->getMessage() ?: '系统繁忙';

        $traces = [];
        if (config("app.debug") && Functions::isDev()) {
            $traces = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack' => explode("\n", $e->getTraceAsString())
            ];
        }

        if ($e instanceof HttpRequestFailed) {
            $message = "服务暂不可使用";
        } elseif ($e instanceof RedisException) {
            $message = "服务暂不可用";
        } elseif ($e instanceof RuntimeException) {
            $message = "服务暂不可用";
        } elseif ($e instanceof LogicException) {
            $message = "服务暂不可用";
        } elseif ($e instanceof Error) {
            $message = "服务暂不可用";
        }

        $data = Controller::error($message, [], (int)$code, $traces);
        $response = new JsonResponse(
            $data,
            $this->isHttpException($e) ? $e->getStatusCode() : 200,
            $this->isHttpException($e) ? $e->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
        return $response;
    }

    /**
     * Prepare a response for the given exception.
     *
     * @param Request $request request
     * @param Throwable $e e
     * @return JsonResponse
     */
    protected function prepareResponse($request, Throwable $e)
    {
        return $this->prepareJsonResponse($request, $e);
    }
}
