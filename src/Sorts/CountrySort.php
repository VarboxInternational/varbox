<?php

namespace Varbox\Sorts;

use Varbox\Contracts\CountrySortContract;

class CountrySort extends Sort implements CountrySortContract
{
    /**
     * Get the request field name to sort by.
     *
     * @return string
     */
    public function field()
    {
        return 'sort';
    }

    /**
     * Get the direction to sort by.
     *
     * @return string
     */
    public function direction()
    {
        return 'direction';
    }
}
