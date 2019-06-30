<?php

namespace Varbox\Contracts;

interface StateModelContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities();

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Varbox\Contracts\CountryModelContract|int $country
     */
    public function scopeFromCountry($query, $country);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeAlphabetically($query);
}
