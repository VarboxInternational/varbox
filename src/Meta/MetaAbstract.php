<?php

namespace Varbox\Meta;

abstract class MetaAbstract
{
    /**
     * Build the HTML for the supplied tag keys.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    abstract public static function tag($key, $value);
}
