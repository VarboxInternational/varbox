<?php

namespace Varbox\Tests\Http\Handlers;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Varbox\Exceptions\Handler;
use Varbox\Models\Error;
use Varbox\Tests\Http\TestCase;

class ErrorHandlerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Route::get('/_test/error-handler', function () {
            return abort(404);
        });
    }

    /** @test */
    public function it_saves_the_occurred_error()
    {
        $this->app->instance(ExceptionHandler::class, app(Handler::class));

        $this->app['config']->set('varbox.errors.enabled', true);

        $this->get('/_test/error-handler');

        $this->assertEquals(1, Error::count());
        $this->assertEquals(NotFoundHttpException::class, Error::first()->type);
        $this->assertEquals(404, Error::first()->code);
    }

    /** @test */
    public function it_doesnt_save_the_occurred_error_if_disabled_from_config()
    {
        $this->app->instance(ExceptionHandler::class, app(Handler::class));

        $this->app['config']->set('varbox.errors.enabled', false);

        $this->get('/_test/error-handler');

        $this->assertEquals(0, Error::count());
    }
}
