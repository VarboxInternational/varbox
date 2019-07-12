<?php

namespace Varbox\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Varbox\Contracts\ErrorModelContract;

class ErrorSavedSuccessfully
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var ErrorModelContract
     */
    public $error;

    /**
     * Create a new event instance.
     *
     * @param ErrorModelContract $error
     * @return void
     */
    public function __construct(ErrorModelContract $error)
    {
        $this->error = $error;
    }
}
