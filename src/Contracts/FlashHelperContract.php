<?php

namespace Varbox\Contracts;

use Exception;

interface FlashHelperContract
{
    /**
     * @return string|null
     */
    public function message();

    /**
     * @param string|null $message
     * @return \Illuminate\View\View
     */
    public function success($message = null);

    /**
     * @param string|null $message
     * @param \Exception|null $exception
     * @return \Illuminate\View\View
     */
    public function error($message = null, \Exception $exception = null);

    /**
     * @param string|null $message
     * @param \Exception|null $exception
     * @return \Illuminate\View\View
     */
    public function warning($message = null, \Exception $exception = null);

    /**
     * @param string|null $message
     * @param Exception|null $exception
     * @return \Illuminate\View\View
     */
    public function info($message = null, Exception $exception = null);
}
