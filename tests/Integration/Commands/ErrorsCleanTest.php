<?php

namespace Varbox\Tests\Integration\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Error;
use Varbox\Tests\Integration\TestCase;

class ErrorsCleanTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpTestingConditions();
    }

    /** @test */
    public function it_can_delete_old_errors()
    {
        $this->app['config']->set('varbox.errors.old_threshold', 30);

        $this->assertEquals(4, Error::count());

        $this->artisan('varbox:clean-errors');

        $this->assertEquals(1, Error::count());
    }

    /** @test */
    public function it_doesnt_delete_any_errors_if_the_days_threshold_is_null()
    {
        $this->app['config']->set('varbox.errors.old_threshold', null);

        $this->assertEquals(4, Error::count());

        $this->artisan('varbox:clean-errors');

        $this->assertEquals(4, Error::count());
    }

    /** @test */
    public function it_doesnt_delete_any_errors_if_the_days_threshold_is_zero()
    {
        $this->app['config']->set('varbox.errors.old_threshold', 0);

        $this->assertEquals(4, Error::count());

        $this->artisan('varbox:clean-errors');

        $this->assertEquals(4, Error::count());
    }

    /**
     * @return void
     */
    protected function setUpTestingConditions()
    {
        Error::create([
            'type' => 'Test/Error/Type',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $error = new Error;
            $error->type = 'Test/Error/Type/' . $i;
            $error->created_at = today()->subDays(31);
            $error->save();
        }
    }
}
