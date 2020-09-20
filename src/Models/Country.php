<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Varbox\Contracts\CountryModelContract;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsCsvExportable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class Country extends Model implements CountryModelContract
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
    protected $table = 'countries';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'capital',
        'flag',
    ];

    /**
     * Country has many states.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function states()
    {
        $state = config('varbox.bindings.models.state_model', State::class);

        return $this->hasMany($state, 'country_id');
    }

    /**
     * Country has many cities.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cities()
    {
        $city = config('varbox.bindings.models.city_model', City::class);

        return $this->hasMany($city, 'country_id');
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
            ->withEntityType('country')
            ->withEntityName($this->name)
            ->withEntityUrl(route('admin.countries.edit', $this->getKey()));
    }

    /**
     * Get the heading columns for the csv.
     *
     * @return array
     */
    public function getCsvColumns()
    {
        return [
            'Name', 'Code', 'Capital', 'Created At', 'Last Modified At',
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
            $this->capital,
            $this->created_at->format('Y-m-d H:i:s'),
            $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
