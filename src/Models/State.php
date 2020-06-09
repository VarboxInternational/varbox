<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Varbox\Contracts\CountryModelContract;
use Varbox\Contracts\StateModelContract;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsCsvExportable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class State extends Model implements StateModelContract
{
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
    protected $table = 'states';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id',
        'name',
        'code',
    ];

    /**
     * State belongs to country.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        $country = config('varbox.bindings.models.country_model', Country::class);

        return $this->belongsTo($country, 'country_id');
    }

    /**
     * Country has many cities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities()
    {
        $city = config('varbox.bindings.models.city_model', City::class);

        return $this->hasMany($city, 'state_id');
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
            ->withEntityType('state')
            ->withEntityName($this->name)
            ->withEntityUrl(route('admin.states.edit', $this->getKey()));
    }

    /**
     * Get the heading columns for the csv.
     *
     * @return array
     */
    public function getCsvColumns()
    {
        return [
            'Name', 'Code', 'Country', 'Created At', 'Last Modified At',
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
            strtoupper($this->code),
            $this->country && $this->country->exists ? $this->country->name : 'N/A',
            $this->created_at->format('Y-m-d H:i:s'),
            $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
