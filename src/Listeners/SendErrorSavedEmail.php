<?php

namespace Varbox\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Varbox\Events\ErrorSavedSuccessfully;
use Varbox\Mail\ErrorSavedMail;

class SendErrorSavedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param ErrorSavedSuccessfully $event
     * @return void
     */
    public function handle(ErrorSavedSuccessfully $event)
    {
        $addresses = config('varbox.errors.notification_emails', []);

        if (is_array($addresses) && !empty($addresses)) {
            Mail::to($addresses)->send(new ErrorSavedMail($event->error));
        }
    }
}