<?php

namespace Varbox\Meta;

class MetaTwitter extends Meta
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
        if (in_array($key, config('varbox.meta.available_tags.twitter', []), true)) {
            return '<meta name="' . $key . '" content="' . $value . '" />';
        }

        return '';
    }
}
