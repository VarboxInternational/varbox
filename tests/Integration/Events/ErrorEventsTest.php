<?php

namespace Varbox\Tests\Integration\Events;

use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Varbox\Events\ErrorSavedSuccessfully;
use Varbox\Models\Error;
use Varbox\Tests\Integration\TestCase;

class ErrorEventsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_dispatches_an_error_saved_event_after_creating_the_error_database_record()
    {
        Event::fake();

        (new Error)->saveError(new Exception);

        Event::assertDispatched(ErrorSavedSuccessfully::class);
    }

    /** @test */
    public function it_dispatches_an_error_saved_event_after_updating_the_error_database_record()
    {
        (new Error)->saveError(new Exception);

        Event::fakeFor(function () {
            (new Error)->saveError(new Exception);

            Event::assertDispatched(ErrorSavedSuccessfully::class, 1);
        });
    }
}
