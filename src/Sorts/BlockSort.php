<?php

namespace Varbox\Sorts;

use Varbox\Contracts\BlockSortContract;

class BlockSort extends Sort implements BlockSortContract
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
