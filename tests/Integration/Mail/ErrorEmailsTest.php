<?php

namespace Varbox\Tests\Integration\Mail;

use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Varbox\Mail\ErrorSavedMail;
use Varbox\Models\Error;
use Varbox\Tests\Integration\TestCase;

class ErrorEmailsTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_doesnt_send_any_notification_emails_if_the_config_value_is_empty()
    {
        $this->app['config']->set('varbox.errors.notification_emails', []);

        Mail::fake();

        (new Error)->saveError(new Exception);

        Mail::assertNotQueued(ErrorSavedMail::class);
    }

    /** @test */
    public function it_sends_an_error_saved_notification_email_to_all_addresses_set_in_config()
    {
        $this->app['config']->set('varbox.errors.notification_emails', [
            'test@mail.com', 'another.test@mail.com'
        ]);

        Mail::fake();

        (new Error)->saveError(new Exception);

        Mail::assertQueued(ErrorSavedMail::class, function ($mail) {
            return $mail->hasTo('test@mail.com') &&
                $mail->hasTo('another.test@mail.com');
        });
    }
}
