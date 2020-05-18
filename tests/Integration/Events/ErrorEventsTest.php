<?php

namespace Varbox\Tests\Integration\Events;

use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Mockery;
use Varbox\Events\ErrorSavedSuccessfully;
use Varbox\Listeners\SendErrorSavedEmail;
use Varbox\Models\Error;
use Varbox\Tests\Integration\TestCase;

class ErrorEventsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('varbox.errors.enabled', true);
    }

    /** @test */
    public function it_dispatches_an_error_saved_event_after_creating_the_error_database_record()
    {
        Event::fake();

        $error = (new Error)->saveError(new Exception);

        Event::assertDispatched(ErrorSavedSuccessfully::class, function ($event) use ($error) {
            return $error->id == $event->error->id;
        });
    }

    /** @test */
    public function it_dispatches_an_error_saved_event_after_updating_the_error_database_record()
    {
        (new Error)->saveError(new Exception);

        Event::fakeFor(function () {
            $error = (new Error)->saveError(new Exception);

            Event::assertDispatched(ErrorSavedSuccessfully::class, function ($event) use ($error) {
                return $error->id == $event->error->id;
            });
        });
    }

    /** @test */
    public function it_triggers_the_listener_to_send_the_notification_emails_upon_dispatching_the_error_saved_event()
    {
        $listener = Mockery::spy(SendErrorSavedEmail::class);

        $this->app->instance(SendErrorSavedEmail::class, $listener);

        $error = (new Error)->saveError(new Exception);

        $listener->shouldHaveReceived('handle')->with(Mockery::on(function ($event) use ($error) {
            return $error->id == $event->error->id;
        }))->once();
    }
}
