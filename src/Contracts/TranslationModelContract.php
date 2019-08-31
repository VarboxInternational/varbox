<?php

namespace Varbox\Contracts;

interface TranslationModelContract
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $group
     * @return mixed
     */
    public function scopeOfGroup($query, $group);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $group
     * @return mixed
     */
    public function scopeWithoutGroup($query, $group);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeOrderByGroupThenKeys($query);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeDistinctGroup($query);
}
