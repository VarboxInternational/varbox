<?php

namespace Varbox\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface PaginationHelperContract
{
    /**
     * @param \Illuminate\Pagination\LengthAwarePaginator $items
     * @param array $data
     * @return string
     */
    public function render(LengthAwarePaginator $items, array $data = []);
}
