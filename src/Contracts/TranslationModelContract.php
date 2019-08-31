<?php

namespace Varbox\Contracts;

interface TranslationModelContract
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $group
     * @return mixed
     */
    public function scopeOfTranslatedGroup($query, $group);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeOrderByGroupKeys($query);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeDistinctGroup($query);
}
