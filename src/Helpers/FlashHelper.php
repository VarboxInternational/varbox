<?php

namespace Varbox\Helpers;

use Exception;
use Illuminate\Support\Arr;
use Varbox\Contracts\FlashHelperContract;

class FlashHelper implements FlashHelperContract
{
    /**
     * The flash type to be rendered.
     * The types are coming from "/config/varbox/flash.php" -> "types".
     * The message() method on this helper will try to display the view with the name of this property.
     *
     * @var string
     */
    protected $type;

    /**
     * Set the pagination type (view) to render.
     *
     * @param string|null $type
     */
    public function __construct($type = null)
    {
        $this->type = $type ?: Arr::first(config('varbox.flash.types'), null, 'default');
    }

    /**
     * Render any flash message if it's set.
     *
     * @return string|null
     */
    public function message()
    {
        if (session()->has('flash_success')) {
            $this->success();
        }

        if (session()->has('flash_error')) {
            $this->error();
        }

        if (session()->has('flash_warning')) {
            $this->warning();
        }

        if (session()->has('flash_info')) {
            $this->info();
        }

        $this->show(null, null);
    }

    /**
     * Set or render the success flash message.
     *
     * @param string|null $message
     * @return void
     */
    public function success($message = null)
    {
        if ($message === null) {
            $this->show('success', session('flash_success'));
            return;
        }

        session()->flash('flash_success', $message);
    }

    /**
     * Set or render the error flash message.
     *
     * @param string|null $message
     * @param Exception|null $exception
     * @return void
     */
    public function error($message = null, Exception $exception = null)
    {
        if ($message === null) {
            $this->show('danger', session('flash_error'));
            return;
        }

        session()->flash('flash_error', $message);

        if (config('varbox.flash.log_errors', true) && $exception) {
            logger()->error($exception);
        }
    }

    /**
     * Set or render the warning flash message.
     *
     * @param string|null $message
     * @param Exception|null $exception
     * @return void
     */
    public function warning($message = null, Exception $exception = null)
    {
        if ($message === null) {
            $this->show('warning', session('flash_warning'));
            return;
        }

        session()->flash('flash_warning', $message);

        if (config('varbox.flash.log_errors', true) && $exception) {
            logger()->warning($exception);
        }
    }

    /**
     * Set or render the warning flash message.
     *
     * @param string|null $message
     * @param Exception|null $exception
     * @return void
     */
    public function info($message = null, Exception $exception = null)
    {
        if ($message === null) {
            $this->show('info', session('flash_info'));
            return;
        }

        session()->flash('flash_info', $message);

        if (config('varbox.flash.log_errors', true) && $exception) {
            logger()->info($exception);
        }
    }

    /**
     * Render the actual view helper that displays flash messages.
     *
     * @param string $type
     * @param string $message
     */
    protected function show($type, $message)
    {
        echo view()->make("varbox::helpers.flash.{$this->type}")->with([
            'type' => $type,
            'message' => $message,
        ])->render();
    }
}
