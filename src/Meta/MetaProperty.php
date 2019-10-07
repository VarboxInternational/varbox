<?php

namespace Varbox\Meta;

class MetaProperty extends Meta
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
        if (in_array($key, config('varbox.meta.available_tags.og', []), true)) {
            return '<meta property="' . $key . '" content="' . $value . '" />';
        }

        return '';
    }
}
