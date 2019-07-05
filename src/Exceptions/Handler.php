<?php

namespace Varbox\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Varbox\Contracts\ErrorModelContract;

class Handler extends ExceptionHandler
{
    /**
     * Report or log an exception.
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        app(ErrorModelContract::class)->saveError($exception);

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
        return parent::render($request, $exception);
    }
}
