<?php

namespace Varbox\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Varbox\Events\ErrorSaved;
use Varbox\Mail\ErrorSavedMail;

class SendErrorSavedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param ErrorSaved $event
     * @return void
     */
    public function handle(ErrorSaved $event)
    {
        $addresses = config('varbox.errors.notification_emails', []);

        if (is_array($addresses) && !empty($addresses)) {
            Mail::to($addresses)->send(new ErrorSavedMail($event->error));
        }
    }
}