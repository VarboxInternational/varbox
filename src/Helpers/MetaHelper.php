<?php

namespace Varbox\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Varbox\Contracts\MetaHelperContract;
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
     * @return void
     */
    public function set($key, $value)
    {
        $this->meta[$key] = self::sanitize($value);
    }

    /**
     * Get a meta property by it's key.
     * If the meta property does not have any value, the default one from the config will be returned.
     *
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        if (empty($this->meta[$key])) {
            return config('varbox.meta.default_values.' . $key, '');
        }

        return $this->meta[$key];
    }

    /**
     * Get the HTML format for a meta property by it's key.
     * All property types will be built for that key: tag, name, og property, twitter card.
     * If the meta property does not have any value, it will use the default value to build the HTML.
     *
     * @param string $key
     * @return string
     */
    public function tag($key)
    {
        if (!($value = $this->get($key))) {
            return '';
        }

        $html = [];

        $html[] = MetaTag::tag($key, $value);
        $html[] = MetaProperty::tag($key, $value);
        $html[] = MetaTwitter::tag($key, $value);

        return implode('', $html);
    }

    /**
     * Get the HTML format for multiple meta properties by their keys.
     *
     * @param array|string|null $keys
     * @return string
     */
    public function tags(...$keys)
    {
        $keys = empty($keys) ? array_keys($this->meta) : Arr::flatten($keys);
        $html = [];

        foreach ($keys as $key) {
            $html[] = $this->tag($key);
        }

        return implode('', $html);
    }

    /**
     * Render the view responsible for displaying all the meta fields.
     * This is to be used in an admin form.
     *
     * @param Model|null $model
     * @return \Illuminate\View\View
     */
    public function fields(Model $model = null)
    {
        return view('varbox::helpers.meta.fields')->with([
            'model' => $model,
        ]);
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
