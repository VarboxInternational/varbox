<?php

namespace Varbox\Meta;

class MetaTwitter extends MetaAbstract
{
    /**
     * The available meta keys to be built with this class.
     *
     * @var array
     */
    protected static $available = [
        'card',
        'site',
        'site:id',
        'creator',
        'creator:id',
        'description',
        'title',
        'image',
        'image:alt',
        'player',
        'player:width',
        'player:height',
        'player:stream',
        'app:name:iphone',
        'app:id:iphone',
        'app:url:iphone',
        'app:name:ipad',
        'app:id:ipad',
        'app:url:ipad',
        'app:name:googleplay',
        'app:id:googleplay',
        'app:url:googleplay'
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
            return '<meta name="twitter:' . $key . '" content="' . $value . '" />';
        }

        return '';
    }
}
