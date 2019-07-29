<?php

namespace Varbox\Mail;

use Illuminate\Mail\Mailable as BaseMailable;
use Varbox\Contracts\EmailModelContract;

abstract class Mailable extends BaseMailable
{
    /**
     * The loaded email model.
     *
     * @var EmailModelContract
     */
    protected $email;

    /**
     * Get the type of the email.
     * It must be a string representing one of the keys from "config/varbox/emails.php" -> "types" config option.
     *
     * @return string
     */
    abstract protected function emailType();

    /**
     * Get the subject of the email.
     *
     * @return string
     */
    abstract protected function defaultSubject();

    /**
     * Return an array containing:
     * - keys: each variable name as defined inside the "config/varbox/emails.php" -> "variables" config option
     * - values: the actual value that the respective variable (key) should be transformed to
     *
     * @return array
     */
    abstract protected function mapVariables();

    /**
     * @throws \Varbox\Exceptions\EmailException
     * @return void
     */
    public function build()
    {
        $this->email = app(EmailModelContract::class)->findByType($this->emailType());

        $this->replyTo($this->getReplyTo());
        $this->from($this->getFromAddress(), $this->getFromName());
        $this->subject($this->getSubject() ?: $this->defaultSubject());

        $this->markdown($this->email->getView(), [
            'message' => $this->parseMessage(),
        ]);

        if (($attachment = $this->getAttachment()) && uploaded($attachment)->exists()) {
            $this->attach(uploaded($attachment)->path(null, true), [
                'as' => uploaded($attachment)->load()->original_name,
            ]);
        }
    }

    /**
     * Get the "reply to" for the loaded email.
     *
     * @return string|null
     */
    protected function getReplyTo()
    {
        return optional($this->email)->reply_to ?: config('mail.from.address');
    }

    /**
     * Get the "from address" for the loaded email.
     *
     * @return string|null
     */
    protected function getFromAddress()
    {
        return optional($this->email)->from_address ?: config('mail.from.address');
    }

    /**
     * Get the "from name" for the loaded email.
     *
     * @return string|null
     */
    protected function getFromName()
    {
        return optional($this->email)->from_name ?: config('mail.from.name');
    }

    /**
     * Get the "subject" for the loaded email.
     *
     * @return string|null
     */
    protected function getSubject()
    {
        return optional($this->email)->subject;
    }

    /**
     * Get the "attachment" for the loaded email.
     *
     * @return string|null
     */
    protected function getAttachment()
    {
        return optional($this->email)->attachment;
    }

    /**
     * Parse the message for used variables.
     * Replace the variable names with the relevant content.
     *
     * @return mixed
     */
    protected function parseMessage()
    {
        $message = $this->email->message;

        foreach ($this->mapVariables() as $varName => $varValue) {
            $message = str_replace('[' . trim($varName, '[]') . ']', $varValue, $message);
        }

        return $message;
    }
}
