<?php

namespace Varbox\Options;

use Exception;
use Illuminate\Database\Eloquent\Model;

class BlockOptions
{
    /**
     * The locations available to assign blocks in.
     *
     * @var array
     */
    private $locations;

    /**
     * The cache key in use.
     *
     * @var Model|string
     */
    private $inherit;

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
            'The property "' . $name . '" does not exist in class "' . static::class . '"'
        );
    }

    /**
     * Get a fresh instance of this class.
     *
     * @return BlockOptions
     */
    public static function instance(): BlockOptions
    {
        return new static();
    }

    /**
     * Set the locations to work with in the Varbox\Traits\HasBlocks trait
     *
     * @param callable|array|string|null
     * @return BlockOptions
     */
    public function withLocations($locations = null): BlockOptions
    {
        switch ($locations) {
            case is_callable($locations):
                $this->locations = call_user_func($locations);
                break;
            case is_string($locations):
                $this->locations = explode(',', $locations);
                break;
            case is_array($locations):
                $this->locations = $locations;
                break;
            default:
                $this->locations = null;
                break;
        }

       return $this;
    }

    /**
     * Set the inherit to work with in the Varbox\Traits\HasBlocks trait.
     *
     * @param callable|string $inherit
     * @return BlockOptions
     */
    public function inheritFrom($inherit): BlockOptions
    {
        if (is_callable($inherit)) {
            $this->inherit = call_user_func($inherit);
        } else {
            $this->inherit = $inherit;
        }


        return $this;
    }
}
