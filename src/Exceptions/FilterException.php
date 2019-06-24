<?php

namespace Varbox\Exceptions;

use Exception;

class FilterException extends Exception
{
    /**
     * The exception to be thrown when no "operator" has been supplied to the filtering functionality via a Filter object.
     *
     * @param string $field
     * @param string $class
     * @return static
     */
    public static function noOperatorSupplied($field, $class)
    {
        return new static(
            'For each field declared as filterable, you must specify an operator type.' . PHP_EOL .
            'Please specify an operator for "' . $field . '" in "' . $class . '".' . PHP_EOL .
            'Example: ---> "field" => "...operator:like..."'
        );
    }

    /**
     * The exception to be thrown when no "condition" has been supplied to the filtering functionality via a Filter object.
     *
     * @param string $field
     * @param string $class
     * @return static
     */
    public static function noConditionSupplied($field, $class)
    {
        return new static(
            'For each field declared as filterable, you must specify a condition type.' . PHP_EOL .
            'Please specify a condition for "' . $field . '" in "' . $class . '".' . PHP_EOL .
            'Example: ---> "field" => "...condition:or..."'
        );
    }

    /**
     * The exception to be thrown when no "columns" have been supplied to the filtering functionality via a Filter object.
     *
     * @param string $field
     * @param string $class
     * @return static
     */
    public static function noColumnsSupplied($field, $class)
    {
        return new static(
            'For each field declared as filterable, you must specify the used columns.' . PHP_EOL .
            'Please specify the columns for "' . $field . '" in "' . $class . '"' . PHP_EOL .
            'Example: ---> "field" => "...columns:name,content..."'
        );
    }
}
