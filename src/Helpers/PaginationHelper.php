<?php

namespace Varbox\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Varbox\Contracts\PaginationHelperContract;

class PaginationHelper implements PaginationHelperContract
{
    /**
     * The pagination type to be rendered.
     * For now, only "default" and "admin" are available.
     * The render() method on this helper will try to display the view with the name of this property.
     *
     * @var string
     */
    protected $type;

    /**
     * Set the pagination type (view) to render.
     *
     * @param string|null $type
     */
    public function __construct($type = null)
    {
        $this->type = $type ?: Arr::first(config('varbox.varbox-pagination.types'), null, 'default');
    }

    /**
     * Display the pagination view helper.
     *
     * @param LengthAwarePaginator $items
     * @param array $data
     * @return string
     */
    public function render(LengthAwarePaginator $items, array $data = [])
    {
        return $items->links("varbox::helpers.pagination.{$this->type}", $data);
    }
}
