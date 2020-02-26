<?php

namespace Varbox\Tests\Http\Middleware;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Varbox\Middleware\PersistLocale;
use Varbox\Tests\Http\TestCase;

class PersistLocaleTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app->make(Kernel::class)->pushMiddleware(StartSession::class);
        $this->app->make(Kernel::class)->pushMiddleware(PersistLocale::class);

        Route::middleware('web')->get('/_test/route-1', function () {
            return app()->getLocale();
        });

        Route::get('/_test/route-2', function () {
            return app()->getLocale();
        });
    }

    /** @test */
    public function it_persists_the_set_locale_between_requests()
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/_test/route-1');
        $this->assertEquals('en', $response->getContent());

        session()->put('locale', 'ro');

        $response = $this->get('/_test/route-1');
        $this->assertEquals('ro', $response->getContent());

        $response = $this->get('/_test/route-2');
        $this->assertEquals('ro', $response->getContent());
    }

    /** @test */
    public function it_auto_detect_the_preferred_locale()
    {
        $this->withoutExceptionHandling();

        $this->app['config']->set('varbox.translation.auto_detect_locale', true);

        $this->app->setLocale('not this one');

        $response = $this->get('/_test/route-1');
        $this->assertEquals('en_US', $response->getContent());
    }
}
