<?php

namespace Varbox\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Varbox\Exceptions\CrudException;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasNodes;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;
use Varbox\Contracts\MenuModelContract;

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
        'route',
        'data',
        'active',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'active' => 'boolean',
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
                    $model->attributes['route'] = null;

                    break;
                case 'route':
                    $model->attributes['menuable_id'] = null;
                    $model->attributes['menuable_type'] = null;
                    $model->attributes['url'] = null;

                    break;
                default:
                    $types = (array)config('varbox.menus.types', []);

                    $model->attributes['url'] = null;
                    $model->attributes['route'] = null;
                    $model->attributes['menuable_type'] = $types[$model->attributes['type']];

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
     * If the "url" column contains any value, return that;
     * Else, if the "route" column contains any value, return the route's url;
     * Else, match the "entity_id" and "entity_type" on a record and return it's url.
     *
     * @return string|null
     */
    public function getUrlAttribute()
    {
        if ($this->attributes['url']) {
            return Str::startsWith($this->attributes['url'], ['http', 'www']) ?
                $this->attributes['url'] : url($this->attributes['url']);
        }

        if ($this->attributes['route']) {
            return route($this->attributes['route'], [], true);
        }

        if ($this->menuable && $this->menuable->url) {
            return $this->menuable->getUrl();
        }

        return null;
    }

    /**
     * Get the uri of the menu.
     * If the "url" column contains any value, return that;
     * Else, if the "route" column contains any value, return the route's url;
     * Else, match the "entity_id" and "entity_type" on a record and return it's url.
     *
     * @return string|null
     */
    public function getUriAttribute()
    {
        if ($this->attributes['url']) {
            return Str::startsWith($this->attributes['url'], ['http', 'www']) ?
                $this->attributes['url'] : '/' . trim($this->attributes['url'], '/');
        }

        if ($this->attributes['route']) {
            return route($this->attributes['route'], [], false);
        }

        if ($this->menuable && $this->menuable->url) {
            return $this->menuable->getUri();
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
     * Get all the eligible routes that can be assigned as a menu url.
     *
     * - exclude routes without a name
     * - exclude non GET routes
     * - exclude routes that are not in the "web" scope
     * - exclude routes from admin
     * - exclude routes with parameters
     * - exclude the last route (the fallback route)
     *
     * @return array
     */
    public static function getRoutes()
    {
        $routes = [];

        foreach (Route::getRoutes() as $route) {
            if (
                !$route->getName() ||
                !in_array('get', array_map('strtolower', $route->methods())) ||
                !in_array('web', array_map('strtolower', $route->middleware())) ||
                Str::startsWith($route->getPrefix(), config('varbox.admin.prefix', 'admin')) ||
                Str::contains($route->uri(), ['{', '}'])
            ) {
                continue;
            }

            $routes[] = $route;
        }

        array_pop($routes);

        return $routes;
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
            ->withEntityUrl(route('admin.menus.edit', [
                'location' => $this->location,
                'id' => $this->getKey()
            ]));
    }
}
