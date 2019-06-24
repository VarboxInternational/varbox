<?php

namespace Varbox\Meta;

class MetaProperty extends MetaAbstract
{
    /**
     * The available meta keys to be built with this class.
     *
     * @var array
     */
    protected static $available = [
        'title',
        'description',
        'type',
        'url',
        'image',
        'audio',
        'video',
        'locale',
        'determiner',
        'site_name',
    ];

    /**
     * Build the HTML for the supplied tag keys.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    public static function tag($key, $value)
    {
        if (in_array($key, self::$available, true)) {
            return '<meta property="og:' . $key . '" content="' . $value . '" />';
        }

        return '';
    }
}
