<?php

namespace Varbox\Contracts;

interface TranslationModelContract
{
    /**
     * @param string $value
     */
    public function setValueAttribute($value);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeHavingValue($query);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeWithoutValue($query);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $group
     * @return mixed
     */
    public function scopeWithGroup($query, $group);

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
