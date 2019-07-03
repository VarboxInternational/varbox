<?php

namespace Varbox\Tests\Integration\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Varbox\Models\User;
use Varbox\Tests\Integration\TestCase;

class NotificationsCleanTest extends TestCase
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
    public function it_can_delete_old_notifications()
    {
        $this->app['config']->set('varbox.varbox-notification.old_threshold', 30);

        $this->assertEquals(4, DatabaseNotification::count());

        $this->artisan('varbox:clean-notifications');

        $this->assertEquals(1, DatabaseNotification::count());
    }

    /** @test */
    public function it_doesnt_delete_any_notifications_if_the_days_threshold_is_null()
    {
        $this->app['config']->set('varbox.varbox-notification.old_threshold', null);

        $this->assertEquals(4, DatabaseNotification::count());

        $this->artisan('varbox:clean-notifications');

        $this->assertEquals(4, DatabaseNotification::count());
    }

    /** @test */
    public function it_doesnt_delete_any_notifications_if_the_days_threshold_is_zero()
    {
        $this->app['config']->set('varbox.varbox-notification.old_threshold', 0);

        $this->assertEquals(4, DatabaseNotification::count());

        $this->artisan('varbox:clean-notifications');

        $this->assertEquals(4, DatabaseNotification::count());
    }

    /**
     * @return void
     */
    protected function setUpTestingConditions()
    {
        $user = User::create([
            'email' => 'test-user@mail.com',
            'password' => bcrypt('test_password'),
        ]);

        $user->notifications()->create([
            'id' => Str::uuid()->toString(),
            'type' => 'test-notification-type',
            'data' => [
                'subject' => 'Test Subject',
                'url' => 'http://test-url.tld',
            ]
        ]);

        for ($i = 1; $i <= 3; $i++) {
            $user->notifications()->create([
                'id' => Str::uuid()->toString(),
                'type' => 'test-notification-type',
                'created_at' => today()->subDays(31),
                'data' => [
                    'subject' => 'Test Subject',
                    'url' => 'http://test-url.tld',
                ]
            ]);
        }
    }
}
