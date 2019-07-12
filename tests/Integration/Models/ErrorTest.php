<?php

namespace Varbox\Tests\Integration\Models;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Varbox\Models\Error;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class ErrorTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var Error
     */
    protected $error;

    /**
     * @var Exception
     */
    protected $exception;

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Error::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Error::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Error::class));
    }

    /** @test */
    public function it_determines_if_the_error_should_be_saved()
    {
        $this->initErrorAndException();

        $this->app['config']->set('varbox.errors.enabled', false);

        $this->assertFalse((new Error)->shouldSaveError($this->exception));

        $this->app['config']->set('varbox.errors.enabled', true);

        $this->assertTrue((new Error)->shouldSaveError($this->exception));

        $this->app['config']->set('varbox.errors.ignore_errors', [NotFoundHttpException::class]);

        $this->assertFalse((new Error)->shouldSaveError($this->exception));

        $this->app['config']->set('varbox.errors.ignore_errors', []);

        $this->assertTrue((new Error)->shouldSaveError($this->exception));
    }

    /** @test */
    public function it_stores_an_error_when_it_occurres()
    {
        $this->initErrorAndException();

        $this->assertEquals(0, $this->error->count());

        $this->error->saveError($this->exception);

        $this->assertEquals(1, $this->error->count());

        $error = $this->error->first();

        $this->assertEquals(NotFoundHttpException::class, $error->type);
        $this->assertEquals(404, $error->code);
        $this->assertEquals(1, $error->occurrences);
    }

    /** @test */
    public function it_updates_the_occurred_error_if_it_has_occurred_before_in_the_same_place()
    {
        $this->initErrorAndException();

        $this->assertEquals(0, $this->error->count());

        $this->error->saveError($this->exception);
        $this->error->saveError($this->exception);

        $this->assertEquals(1, $this->error->count());

        $error = $this->error->first();

        $this->assertEquals(NotFoundHttpException::class, $error->type);
        $this->assertEquals(404, $error->code);
        $this->assertEquals(2, $error->occurrences);
    }

    /** @test */
    public function it_can_delete_old_occurred_errrors()
    {
        $this->app['config']->set('varbox.errors.old_threshold', 30);

        $this->initErrorAndException();

        $this->error->saveError($this->exception);
        $this->error->saveError(new Exception);

        $error = $this->error->first();
        $error->created_at = today()->subDays(31);
        $error->save();

        $this->assertEquals(2, $this->error->count());

        $this->error->deleteOld();

        $this->assertEquals(1, $this->error->count());
    }

    /**
     * @return void
     */
    protected function initErrorAndException()
    {
        $this->error = new Error;
        $this->exception = new NotFoundHttpException('Page not found', null, 404);
    }
}
