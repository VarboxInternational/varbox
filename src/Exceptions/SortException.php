<?php

namespace Varbox\Exceptions;

use Exception;
use Varbox\Sorts\Sort;

class SortException extends Exception
{
    /**
     * The exception to be thrown when an invalid direction is supplied as the argument.
     *
     * @param string $direction
     * @return static
     */
    public static function invalidDirectionSupplied($direction)
    {
        return new static(
            'Invalid sorting direction.' . PHP_EOL .
            'You provided the direction: "' . $direction . '".' . PHP_EOL .
            'Please provide one of these directions: ' . implode('|', Sort::$directions) . '.'
        );
    }

    /**
     * The exception to be thrown when trying to sort by an invalid relation type.
     *
     * @param string$relation
     * @param string $type
     * @return static
     * @internal param string $direction
     */
    public static function wrongRelationToSort($relation, $type)
    {
        return new static(
            'You can only sort records by the following relations: HasOne, BelongsTo.' . PHP_EOL .
            'The relation "' . $relation . '" is of type ' . $type . ' and cannot be sorted by.'
        );
    }
}
