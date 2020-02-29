<?php

namespace Varbox\Options;

use Exception;

class OrderOptions
{
    /**
     * The database field that will contain the order position of a record in set.
     *
     * @var string
     */
    private $orderColumn = 'ord';

    /**
     * Flag indicating whether or not automatic ordering on creating should be done.
     *
     * @var bool
     */
    private $orderWhenCreating = true;

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
     * @return OrderOptions
     */
    public static function instance(): OrderOptions
    {
        return new static();
    }

    /**
     * Set the $orderColumn to work with in the App\Traits\IsOrderable trait.
     *
     * @param string $column
     * @return OrderOptions
     */
    public function setOrderColumn($column): OrderOptions
    {
        $this->orderColumn = $column;

        return $this;
    }

    /**
     * Set the $orderWhenCreating to work with in the App\Traits\IsOrderable trait.
     *
     * @return OrderOptions
     */
    public function doNotOrderWhenCreating(): OrderOptions
    {
        $this->orderWhenCreating = false;

        return $this;
    }
}
