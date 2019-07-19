<?php

namespace Varbox\Contracts;

interface UploadedHelperContract
{
    /**
     * @param string $file
     */
    public function __construct($file);

    /**
     * @param int|null $number
     * @return string
     */
    public function thumbnail($number = null);

    /**
     * @param string|null $style
     * @return string
     */
    public function url($style = null);

    /**
     * @param string|null $style
     * @param bool $full
     * @return string
     */
    public function path($style = null, $full = false);

    /**
     * @return bool
     */
    public function exists();
}
