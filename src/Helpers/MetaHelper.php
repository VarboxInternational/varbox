<?php

namespace Varbox\Helpers;

use Varbox\Contracts\MetaHelperContract;
use Varbox\Meta\MetaName;
use Varbox\Meta\MetaProperty;
use Varbox\Meta\MetaTag;
use Varbox\Meta\MetaTwitter;

class MetaHelper implements MetaHelperContract
{
    /**
     * The container for all the meta keys available to render.
     * This is built using the set() method.
     * You can get the contents of this by using get(), tag(), tags().
     *
     * @var array
     */
    protected $meta = [];

    /**
     * Set a meta property using the key and value provided.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    public function set($key, $value)
    {
        $value = self::sanitize($value);

        if (strtolower($key) == 'image') {
            $this->meta['image'][] = $value;
        } else {
            $this->meta[$key] = $value;
        }
    }

    /**
     * Get a meta property by it's key.
     * If the meta property does not have any value, the default one will be returned.
     *
     * @param string $key
     * @param array|string|null $default
     * @return string
     */
    public function get($key, $default = null)
    {
        if (empty($this->meta[$key])) {
            return $default;
        }

        return $this->meta[$key];
    }

    /**
     * Get the HTML format for a meta property by it's key.
     * All property types will be built for that key: tag, name, og property, twitter card.
     * If the meta property does not have any value, it will use the default value to build the HTML.
     *
     * @param string $key
     * @param array|string|null $default
     * @return string
     */
    public function tag($key, $default = null)
    {
        if (!($values = $this->get($key, $default))) {
            return '';
        }

        if (!is_array($values)) {
            $values = [$values];
        }

        $html = '';

        foreach ($values as $value) {
            $html .= MetaTag::tag($key, $value);
            $html .= MetaName::tag($key, $value);
            $html .= MetaProperty::tag($key, $value);
            $html .= MetaTwitter::tag($key, $value);
        }

        return $html;
    }

    /**
     * Get the HTML format for multiple meta properties by their keys.
     *
     * @param $keys
     * @return string
     */
    public function tags(...$keys)
    {
        $keys = array_flatten($keys);
        $html = '';

        foreach ($keys as $key) {
            $html .= $this->tag($key);
        }

        return $html;
    }

    /**
     * Sanitize a string for safe usage inside <meta> tags.
     *
     * @param string $text
     * @return string
     */
    protected static function sanitize($text)
    {
        return trim(str_replace('"', '&quot;', preg_replace('/[\r\n\s]+/', ' ', strip_tags($text))));
    }
}
