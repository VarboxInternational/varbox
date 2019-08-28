<?php

namespace Varbox\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Varbox\Exceptions\CrudException;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasNodes;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;
use Varbox\Contracts\MenuModelContract;
use Varbox\Exceptions\MenuException;

class Menu extends Model implements MenuModelContract
{
    use HasNodes;
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'menus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'menuable_id',
        'menuable_type',
        'type',
        'location',
        'name',
        'url',
        'data',
        'active',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * Boot the model.
     *
     * On save pre-fill the additional data for the specified type.
     * For url type, make the relation fields null.
     * For any other type build the relation fields accordingly.
     *
     * On delete verify if menu has children.
     * If it does, don't delete the menu and throw an exception.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (Model $model) {
            if (!isset($model->attributes['type'])) {
                return;
            }

            switch ($model->attributes['type']) {
                case 'url':
                    $model->attributes['menuable_id'] = null;
                    $model->attributes['menuable_type'] = null;

                    break;
                default:
                    $types = static::getTypes();

                    if (!isset($types[$model->attributes['type']])) {
                        throw new Exception(
                            'Cannot create a menu entry of type "' . $types[$model->attributes['type']] . '"' . PHP_EOL .
                            'Please make sure this type exists inside the "config/varbox/menus.php" file.'
                        );
                    }

                    $model->attributes['url'] = null;
                    $model->attributes['menuable_type'] = $types[$model->attributes['type']]['class'];

                    break;
            }
        });

        static::deleting(function (Model $model) {
            if ($model->children()->count() > 0) {
                throw CrudException::deletionRestrictedDueToChildren();
            }
        });
    }

    /**
     * Page belongs to layout.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menuable()
    {
        return $this->morphTo();
    }

    /**
     * Get the url of the menu.
     * If the actual "url" column contains any value, return that.
     * Otherwise, match the "entity_id" and "entity_type" on a record and return it's url.
     * The matched record must implement the HasUrl trait to actually return an url.
     *
     * @return string|null
     */
    public function getUrlAttribute()
    {
        if ($this->attributes['url']) {
            return Str::startsWith($this->attributes['url'], ['http', 'www']) ?
                $this->attributes['url'] : url($this->attributes['url']);
        }

        if ($this->menuable && $this->menuable->url) {
            return Str::startsWith($this->menuable->url->url, ['http', 'www']) ?
                $this->menuable->url->url : url($this->menuable->url->url);
        }

        return null;
    }

    /**
     * Filter the query by the given parent id.
     *
     * @param Builder $query
     * @param MenuModelContract|int $menu
     */
    public function scopeOfParent($query, $menu)
    {
        $query->where('parent_id', $menu instanceof MenuModelContract ? $menu->getKey() : $menu);
    }

    /**
     * Filter the query to return only active results.
     *
     * @param Builder $query
     */
    public function scopeOnlyActive($query)
    {
        $query->where('active', true);
    }

    /**
     * Filter the query to return only inactive results.
     *
     * @param Builder $query
     */
    public function scopeOnlyInactive($query)
    {
        $query->where('active', false);
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
     * Get all menu locations defined inside the "config/varbox/menus.php" file.
     *
     * @return array
     */
    public static function getLocations()
    {
        return (array)config('varbox.menus.locations', []);
    }

    /**
     * Get all menu types defined inside the "config/varbox/menus.php" file.
     *
     * @return array
     */
    public static function getTypes()
    {
        return (array)config('varbox.menus.types', []);
    }

    /**
     * Get all menu locations defined inside the "config/varbox/menus.php" file in a select format.
     * An array containing: "menu location" => "title-cased menu location".
     *
     * @return array
     */
    public static function getLocationsForSelect()
    {
        $locations = [];

        foreach (static::getLocations() as $location) {
            $locations[$location] = title_case(str_replace(['_', '-', '.'], ' ', $location));
        }

        return $locations;
    }

    /**
     * Get all menu types defined inside the "config/varbox/menus.php" file in a select format.
     * An array containing: "menu type" => "title-cased menu type".
     *
     * @return array
     */
    public static function getTypesForSelect()
    {
        $types = [];

        foreach (array_keys(static::getTypes()) as $type) {
            $types[$type] = title_case(str_replace(['_', '-', '.'], ' ', $type));
        }

        return $types;
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('menu')
            ->withEntityName($this->name)
            ->withEntityUrl(route('admin.menus.edit', $this->getKey()));
    }
}
