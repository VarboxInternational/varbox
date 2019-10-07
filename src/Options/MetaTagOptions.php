<?php

namespace Varbox\Options;

use Exception;
use Illuminate\Support\Arr;

class MetaTagOptions
{
    /**
     * The meta fields that have a default value assigned.
     *
     * @var array
     */
    private $defaultMetaValues = [];

    /**
     * Get the value of a property of this class.
     *
     * @param $name
     * @return mixed
     * @throws Exception
     */
    public function __get($name)
    {
        if (property_exists(static::class, $name)) {
            return $this->{$name};
        }

        throw new Exception(
            'The property "'.$name.'" does not exist in class "'.static::class.'"'
        );
    }

    /**
     * Get a fresh instance of this class.
     *
     * @return MetaTagOptions
     */
    public static function instance(): self
    {
        return new static();
    }

    /**
     * Set the $defaultMetaValues to work with in the Varbox\Traits\HasMetaTags trait.
     *
     * @param string $tag
     * @param string $value
     * @return MetaTagOptions
     */
    public function useDefaultFor($tag, $value): self
    {
        $this->defaultMetaValues[$tag] = $value;

        return $this;
    }
}
