<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Varbox\Contracts\CityModelContract;
use Varbox\Contracts\CountryModelContract;
use Varbox\Contracts\StateModelContract;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class City extends Model implements CityModelContract
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
    protected $table = 'cities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id',
        'state_id',
        'name',
    ];

    /**
     * City belongs to country.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        $country = config('varbox.bindings.models.country_model', Country::class);

        return $this->belongsTo($country, 'country_id');
    }

    /**
     * City belongs to state.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        $state = config('varbox.bindings.models.state_model', State::class);

        return $this->belongsTo($state, 'state_id');
    }

    /**
     * Filter the query by country.
     *
     * @param Builder $query
     * @param CountryModelContract|int $country
     */
    public function scopeFromCountry($query, $country)
    {
        $query->where('country_id', $country instanceof CountryModelContract ? $country->getKey() : $country);
    }

    /**
     * Filter the query by state.
     *
     * @param Builder $query
     * @param StateModelContract|int $state
     */
    public function scopeFromState($query, $state)
    {
        $query->where('state_id', $state instanceof StateModelContract ? $state->getKey() : $state);
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeAlphabetically($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('city')
            ->withEntityName($this->name)
            ->withEntityUrl(route('admin.cities.edit', $this->getKey()));
    }
}
