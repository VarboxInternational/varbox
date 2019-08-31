<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Varbox\Contracts\TranslationModelContract;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class Translation extends Model implements TranslationModelContract
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
    protected $table = 'translations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'locale',
        'group',
        'key',
        'value',
    ];

    /**
     * Filter the query to show only results belonging to a translation group.
     *
     * @param Builder $query
     * @param string $group
     * @return mixed
     */
    public function scopeOfTranslatedGroup($query, $group)
    {
        return $query->where('group', $group)->whereNotNull('value');
    }

    /**
     * Sort the results alphabetically by group key and then by individual key.
     *
     * @param Builder $query
     * @return mixed
     */
    public function scopeOrderByGroupKeys($query)
    {
        return $query->orderBy('group')->orderBy('key');
    }

    /**
     * Select all distinct translation groups.
     *
     * @param Builder $query
     * @return mixed
     */
    public function scopeDistinctGroup($query)
    {
        return $query->select('group')->distinct();
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('translation')
            ->withEntityName($this->key)
            ->withEntityUrl(route('admin.translations.edit', $this->getKey()));
    }
}
