<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Varbox\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Varbox\Contracts\CityModelContract;
use Varbox\Contracts\CountryModelContract;
use Varbox\Contracts\StateModelContract;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsCsvExportable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class City extends Model implements CityModelContract
{
    use HasFactory;
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use IsCsvExportable;

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
        'latitude',
        'longitude',
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

    /**
     * Get the heading columns for the csv.
     *
     * @return array
     */
    public function getCsvColumns()
    {
        return [
            'Name', 'Latitude', 'Longitude', 'Country', 'State', 'Created At', 'Last Modified At',
        ];
    }

    /**
     * Get the values for a row in the csv.
     *
     * @return array
     */
    public function toCsvArray()
    {
        return [
            $this->name,
            $this->latitude,
            $this->longitude,
            $this->country && $this->country->exists ? $this->country->name : 'N/A',
            $this->state && $this->state->exists ? $this->state->name : 'N/A',
            $this->created_at->format('Y-m-d H:i:s'),
            $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
