<?php

namespace Varbox\Tests\Integration\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Activity;
use Varbox\Tests\Integration\TestCase;

class ActivityCleanTest extends TestCase
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
    public function it_can_delete_old_activity_logs()
    {
        $this->app['config']->set('varbox.activity.old_threshold', 30);

        $this->assertEquals(4, Activity::count());

        $this->artisan('varbox:clean-activity');

        $this->assertEquals(1, Activity::count());
    }

    /** @test */
    public function it_doesnt_delete_any_activity_logs_if_the_days_threshold_is_null()
    {
        $this->app['config']->set('varbox.activity.old_threshold', null);

        $this->assertEquals(4, Activity::count());

        $this->artisan('varbox:clean-activity');

        $this->assertEquals(4, Activity::count());
    }

    /** @test */
    public function it_doesnt_delete_any_activity_logs_if_the_days_threshold_is_zero()
    {
        $this->app['config']->set('varbox.activity.old_threshold', 0);

        $this->assertEquals(4, Activity::count());

        $this->artisan('varbox:clean-activity');

        $this->assertEquals(4, Activity::count());
    }

    /**
     * @return void
     */
    protected function setUpTestingConditions()
    {
        Activity::create([
            'event' => 'created',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            Activity::create([
                'event' => 'created',
                'created_at' => today()->subDays(31),
            ]);
        }
    }
}
