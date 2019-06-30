<?php

namespace Varbox\Contracts;

interface CountryModelContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function states();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities();

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeAlphabetically($query);
}
