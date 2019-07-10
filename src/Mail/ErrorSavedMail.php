<?php

namespace Varbox\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Varbox\Contracts\ErrorModelContract;

class ErrorSavedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var ErrorModelContract
     */
    public $error;

    /**
     * Create a new message instance.
     *
     * @param ErrorModelContract $error
     * @return void
     */
    public function __construct(ErrorModelContract $error)
    {
        $this->error = $error;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(config('app.name') . ' - An error occurred!')
            ->view('varbox::emails.error_saved');
    }
}
