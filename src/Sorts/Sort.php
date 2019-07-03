<?php

namespace Varbox\Sorts;

abstract class Sort
{
    /**
     * In case you apply the sorted scope on a model without an Varbox\Sorts\Sort instance as it's parameter.
     * This constant will act as the default sort field.
     * Meaning that the IsSortable trait will look for this request name when deciding what to sort by.
     *
     * @const
     */
    const DEFAULT_SORT_FIELD = 'sort';

    /**
     * In case you apply the sorted scope on a model without an Varbox\Sorts\Sort instance as it's parameter.
     * This constant will act as the default sorting direction field.
     * Meaning that the IsSortable trait will look for this request name when deciding the sorting direction.
     *
     * @const
     */
    const DEFAULT_DIRECTION_FIELD = 'direction';

    /**
     * The sorting directions available.
     *
     * @const
     */
    const DIRECTION_ASC = 'asc';
    const DIRECTION_DESC = 'desc';
    const DIRECTION_RANDOM = 'random';

    /**
     * List of valid sorting directions.
     *
     * @var array
     */
    public static $directions = [
        self::DIRECTION_ASC,
        self::DIRECTION_DESC,
        self::DIRECTION_RANDOM,
    ];

    /**
     * Get the request field name to sort by.
     *
     * @return string
     */
    abstract public function field();

    /**
     * Get the direction to sort by.
     *
     * @return string
     */
    abstract public function direction();
}
