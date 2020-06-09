<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Varbox\Exceptions\CrudException;
use Varbox\Options\ActivityOptions;
use Varbox\Options\DuplicateOptions;
use Varbox\Options\MetaTagOptions;
use Varbox\Options\RevisionOptions;
use Varbox\Options\UrlOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasDuplicates;
use Varbox\Traits\HasMetaTags;
use Varbox\Traits\HasNodes;
use Varbox\Traits\HasRevisions;
use Varbox\Traits\HasUploads;
use Varbox\Traits\HasUrl;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsCsvExportable;
use Varbox\Traits\IsDraftable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;
use Varbox\Contracts\PageModelContract;
use Varbox\Options\BlockOptions;
use Varbox\Traits\HasBlocks;

class Page extends Model implements PageModelContract
{
    use HasUrl;
    use HasUploads;
    use HasRevisions;
    use HasDuplicates;
    use HasActivity;
    use HasBlocks;
    use HasNodes;
    use HasMetaTags;
    use IsDraftable;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use IsCsvExportable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'pages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'type',
        'data',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'drafted_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Boot the model.
     *
     * On save verify if the selected layout can be assigned to a page of the selected type.
     * On delete verify if page has children. If it does, don't delete the page and throw an exception.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function (PageModelContract $page) {
            if ($page->children()->count() > 0) {
                throw CrudException::deletionRestrictedDueToChildren();
            }
        });
    }

    /**
     * Get the page's action for route definition.
     *
     * @return string
     */
    public function getRouteControllerAttribute()
    {
        $types = (array)config('varbox.pages.types', []);

        return $types[$this->attributes['type']]['controller'] ?? '';
    }

    /**
     * Get the page's action for route definition.
     *
     * @return string
     */
    public function getRouteActionAttribute()
    {
        $types = (array)config('varbox.pages.types', []);

        return $types[$this->attributes['type']]['action'] ?? '';
    }

    /**
     * Filter the query by the given parent id.
     *
     * @param Builder $query
     * @param PageModelContract|int $page
     */
    public function scopeOfParent($query, $page)
    {
        $query->where('parent_id', $page instanceof PageModelContract ? $page->id : $page);
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
     * Get the specific upload config parts for this model.
     *
     * @return array
     */
    public function getUploadConfig()
    {
        return config('varbox.pages.upload', []);
    }

    /**
     * Set the options for the HasUrl trait.
     *
     * @return UrlOptions
     */
    public function getUrlOptions()
    {
        return UrlOptions::instance()
            ->routeUrlTo($this->route_controller, $this->route_action)
            ->generateUrlSlugFrom('slug')
            ->saveUrlSlugTo('slug')
            ->prefixUrlWith(function ($prefix, $model) {
                foreach ($model->ancestors()->withDrafts()->get() as $ancestor) {
                    $prefix[] = $ancestor->slug;
                }

                return implode('/' , (array)$prefix);
            });
    }

    /**
     * Set the options for the HasBlocks trait.
     *
     * @return BlockOptions
     */
    public function getBlockOptions()
    {
        $types = (array)config('varbox.pages.types', []);

        return BlockOptions::instance()
            ->withLocations($types[$this->type]['locations'] ?? null);
    }

    /**
     * Set the options for the HasRevisions trait.
     *
     * @return RevisionOptions
     */
    public function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->relationsToRevision('blocks')
            ->limitRevisionsTo(30);
    }

    /**
     * Set the options for the HasDuplicates trait.
     *
     * @return DuplicateOptions
     */
    public function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->excludeColumns('_lft', '_rgt')
            ->uniqueColumns('name', 'slug')
            ->excludeRelations('parent', 'children', 'url', 'revisions');
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('page')
            ->withEntityName($this->name)
            ->withEntityUrl(route('admin.pages.edit', $this->getKey()));
    }

    /**
     * Set the options for the HasMetaTags trait.
     *
     * @return MetaTagOptions
     */
    public function getMetaTagOptions(): MetaTagOptions
    {
        return MetaTagOptions::instance();
    }

    /**
     * Get the heading columns for the csv.
     *
     * @return array
     */
    public function getCsvColumns()
    {
        return [
            'Name', 'Url', 'Type', 'Published', 'Parent Page', 'Created At', 'Last Modified At',
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
            $this->getUrl(),
            $this->type,
            $this->isDrafted() ? 'No' : 'Yes',
            $this->parent && $this->parent->exists ? $this->parent->name : 'None',
            $this->created_at->format('Y-m-d H:i:s'),
            $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
