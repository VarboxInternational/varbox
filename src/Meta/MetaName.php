<?php

namespace Varbox\Meta;

class MetaName extends MetaAbstract
{
    /**
     * Build the HTML for the supplied tag keys.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    public static function tag($key, $value)
    {
        return '<meta name="' . $key . '" content="' . $value . '" />';
    }
}
