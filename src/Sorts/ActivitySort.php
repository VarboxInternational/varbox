<?php

namespace Varbox\Sorts;

use Varbox\Contracts\ActivitySortContract;

class ActivitySort extends Sort implements ActivitySortContract
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
