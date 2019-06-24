<?php

namespace Varbox\Meta;

use Illuminate\Support\Facades\Config;

class MetaTag extends MetaAbstract
{
    /**
     * The available meta keys to be built with this class.
     *
     * @var array
     */
    protected static $available = [
        'title',
    ];

    /**
     * The custom meta keys to be built with this class.
     * Custom properties require special treatment.
     *
     * @var array
     */
    protected static $custom = [
        'image',
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
            return '<'.$key.'>' . (strtolower($key) == 'title' ? $value . ' - ' . Config::get('app.name', 'Varbox') : $value) . '</'.$key.'>';
        }

        if (in_array($key, self::$custom, true)) {
            return '<link rel="image_src" href="' . $value . '" />';
        }

        return '';
    }
}
