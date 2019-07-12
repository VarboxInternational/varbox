<?php

namespace Varbox\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Varbox\Contracts\AddressModelContract;
use Varbox\Contracts\CityModelContract;
use Varbox\Contracts\CountryModelContract;
use Varbox\Contracts\StateModelContract;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class Address extends Model implements AddressModelContract
{
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'addresses';

    /**
     * The attributes that are protected against mass assign.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'country_id',
        'state_id',
        'city_id',
        'address',
    ];

    /**
     * The relations that are eager-loaded.
     *
     * @var array
     */
    protected $with = [
        'country',
        'state',
        'city',
    ];

    /**
     * Address belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $user = config('varbox.bindings.models.user_model', User::class);

        return $this->belongsTo($user, 'user_id');
    }

    /**
     * Address belongs to a country.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        $country = config('varbox.bindings.models.country_model', Country::class);

        return $this->belongsTo($country, 'country_id');
    }

    /**
     * Address belongs to a state.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        $state = config('varbox.bindings.models.state_model', State::class);

        return $this->belongsTo($state, 'state_id');
    }

    /**
     * Address belongs to a city.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        $city = config('varbox.bindings.models.city_model', City::class);

        return $this->belongsTo($city, 'city_id');
    }

    /**
     * Filter the query user.
     *
     * @param Builder $query
     * @param Authenticatable|int $user
     */
    public function scopeOfUser($query, $user)
    {
        $query->where('user_id', $user instanceof Authenticatable ? $user->getKey() : $user);
    }

    /**
     * Filter the query country.
     *
     * @param Builder $query
     * @param CountryModelContract|int $country
     */
    public function scopeFromCountry($query, $country)
    {
        $query->where('country_id', $country instanceof CountryModelContract ? $country->getKey() : $country);
    }

    /**
     * Filter the query state.
     *
     * @param Builder $query
     * @param StateModelContract|int $state
     */
    public function scopeFromState($query, $state)
    {
        $query->where('state_id', $state instanceof StateModelContract ? $state->getKey() : $state);
    }

    /**
     * Filter the query city.
     *
     * @param Builder $query
     * @param CityModelContract|int $city
     */
    public function scopeFromCity($query, $city)
    {
        $query->where('city_id', $city instanceof CityModelContract ? $city->getKey() : $city);
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('address')
            ->withEntityName(Str::limit($this->address, 30))
            ->withEntityUrl(route('admin.addresses.edit', [
                $this->user->getKey(), $this->getKey()
            ]));
    }
}
