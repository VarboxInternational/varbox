<?php

namespace Varbox\Contracts;

interface PageModelContract
{
    /**
     * @return string
     */
    public function getRouteControllerAttribute();

    /**
     * @return string
     */
    public function getRouteActionAttribute();

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param PageModelContract|int $id
     */
    public function scopeOfParent($query, $id);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeAlphabetically($query);
}
