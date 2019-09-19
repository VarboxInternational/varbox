<?php

namespace Varbox\Options;

use Exception;
use Illuminate\Support\Arr;

class TranslationOptions
{
    /**
     * The fields that should be translated.
     *
     * @var array
     */
    private $translatableFields = [];

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
     * @return TranslationOptions
     */
    public static function instance(): self
    {
        return new static();
    }

    /**
     * Set the $translatableFields to work with in the Varbox\Traits\HasTranslations trait.
     *
     * @param array|string $fields
     * @return TranslationOptions
     */
    public function fieldsToTranslate(...$fields): self
    {
        $this->translatableFields = Arr::flatten($fields);

        return $this;
    }
}
