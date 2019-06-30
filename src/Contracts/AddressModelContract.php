<?php

namespace Varbox\Contracts;

interface AddressModelContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city();

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Contracts\Auth\Authenticatable|int $user
     */
    public function scopeOfUser($query, $user);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Varbox\Contracts\CountryModelContract|int $country
     */
    public function scopeFromCountry($query, $country);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Varbox\Contracts\StateModelContract|int $state
     */
    public function scopeFromState($query, $state);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Varbox\Contracts\CityModelContract|int $city
     */
    public function scopeFromCity($query, $city);
}
