<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Varbox\Options\ActivityOptions;
use Varbox\Options\DuplicateOptions;
use Varbox\Options\RevisionOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasDuplicates;
use Varbox\Traits\HasRevisions;
use Varbox\Traits\HasUploads;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsDraftable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;
use Varbox\Contracts\BlockModelContract;

class Block extends Model implements BlockModelContract
{
    use HasUploads;
    use HasRevisions;
    use HasDuplicates;
    use HasActivity;
    use IsDraftable;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use SoftDeletes;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'blocks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'anchor',
        'data',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at',
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
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleted(function (BlockModelContract $block) {
            if ($block->forceDeleting === true) {
                //$block->blockables()->delete();

                DB::table('blockables')->whereBlockId($block->id)->delete();
            }
        });
    }

    /**
     * Get all of the records of a single entity type that are assigned to this block.
     *
     * @param string $class
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function blockables($class)
    {
        return $this->morphedByMany($class, 'blockable')->withPivot([
            'id', 'location', 'ord'
        ])->withTimestamps();
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
     * Get all block types defined inside the "config/varbox/cms/block.php" file.
     *
     * @return array
     */
    public static function getTypes()
    {
        return (array)config('varbox.blocks.types', []);
    }

    /**
     * Get a list of all block locations.
     * This is done by looking inside each block's composer class -> $locations property.
     *
     * @return array
     */
    public static function getLocations()
    {
        $locations = [];

        foreach (static::getTypes() as $name => $options) {
            $class = app($options['composer_class']);

            foreach ($class::$locations as $location) {
                $locations[$location] = Str::title(str_replace(['_', '-'], ' ', $location));
            }
        }

        return $locations;
    }

    /**
     * Get the formatted block types for a select.
     * Final format will be: type => label.
     *
     * @return array
     */
    public static function getTypesForSelect()
    {
        $types = [];

        foreach (static::getTypes() as $type => $options) {
            $types[$type] = $options['label'];
        }

        return $types;
    }

    /**
     * Get the formatted block classes for a select.
     * Final format will be: class => label.
     *
     * @return array
     */
    public static function getClassesForSelect()
    {
        $types = [];

        foreach (static::getTypes() as $type => $options) {
            $types[$options['composer_class']] = $options['label'];
        }

        return $types;
    }

    /**
     * Get the formatted block view paths for a select.
     * Final format will be: path => label.
     *
     * @return array
     */
    public static function getPathsForSelect()
    {
        $types = [];

        foreach (static::getTypes() as $type => $options) {
            $types[$options['views_class']] = $options['label'];
        }

        return $types;
    }

    /**
     * Get the formatted block types for a select.
     * Final format will be: type => image.
     *
     * @return array
     */
    public static function getImagesForSelect()
    {
        $images = [];

        foreach (static::getTypes() as $type => $options) {
            $images[$type] = $options['preview_image'];
        }

        return $images;
    }

    /**
     * Get the specific upload config parts for this model.
     *
     * @return array
     */
    public function getUploadConfig()
    {
        return config('varbox.blocks.upload', []);
    }

    /**
     * @return RevisionOptions
     */
    public function getRevisionOptions()
    {
        return RevisionOptions::instance()
            ->limitRevisionsTo(100);
    }

    /**
     * Set the options for the HasDuplicates trait.
     *
     * @return DuplicateOptions
     */
    public function getDuplicateOptions()
    {
        return DuplicateOptions::instance()
            ->uniqueColumns('name');
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('block')
            ->withEntityName($this->name)
            ->withEntityUrl(route('admin.blocks.edit', $this->getKey()));
    }
}
