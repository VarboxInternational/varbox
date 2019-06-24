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
        $this->type = $type ?: Arr::first(config('varbox.varbox-flash.types'), null, 'default');
    }

    /**
     * Render any flash message if it's set.
     *
     * @return string|null
     */
    public function message()
    {
        switch (true) {
            case session()->has('flash_success');
                return $this->success();
                break;
            case session()->has('flash_error');
                return $this->error();
                break;
            case session()->has('flash_warning');
                return $this->warning();
                break;
        }

        return $this->show(null, null);
    }

    /**
     * Set or render the success flash message.
     *
     * @param string|null $message
     * @return \Illuminate\View\View
     */
    public function success($message = null)
    {
        if ($message === null) {
            return $this->show('success', session('flash_success'));
        }

        session()->flash('flash_success', $message);
    }

    /**
     * Set or render the error flash message.
     *
     * @param string|null $message
     * @param Exception|null $exception
     * @return \Illuminate\View\View
     */
    public function error($message = null, Exception $exception = null)
    {
        if ($message === null) {
            return $this->show('danger', session('flash_error'));
        }

        session()->flash('flash_error', $message);

        if (config('varbox.varbox-flash.log_errors', true) && $exception) {
            logger()->error($exception);
        }
    }

    /**
     * Set or render the warning flash message.
     *
     * @param string|null $message
     * @param Exception|null $exception
     * @return \Illuminate\View\View
     */
    public function warning($message = null, Exception $exception = null)
    {
        if ($message === null) {
            return $this->show('warning', session('flash_warning'));
        }

        session()->flash('flash_warning', $message);

        if (config('varbox.varbox-flash.log_errors', true) && $exception) {
            logger()->warning($exception);
        }
    }

    /**
     * Render the actual view helper that displays flash messages.
     *
     * @param string $type
     * @param string $message
     * @return \Illuminate\View\View
     */
    protected function show($type, $message)
    {
        return view("varbox::helpers.flash.{$this->type}")->with([
            'type' => $type,
            'message' => $message,
        ]);
    }
}
