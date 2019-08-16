<?php

namespace Varbox\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Varbox\Contracts\BlockModelContract;
use Varbox\Models\Block;
use Varbox\Options\BlockOptions;

trait HasBlocks
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the Varbox\Options\BlockOptions file.
     *
     * @var BlockOptions
     */
    protected $blockOptions;

    /**
     * Set the options for the HasBlocks trait.
     *
     * @return BlockOptions
     */
    abstract public function getBlockOptions(): BlockOptions;

    /**
     * Flag to manually enable/disable the blocks savings only for the current request.
     *
     * @var bool
     */
    protected static $saveBlocks = true;

    /**
     * Boot the trait.
     * Remove blocks on save and delete if one or many locations from model's instance have been changed/removed.
     */
    public static function bootHasBlocks()
    {
        static::saved(function (Model $model) {
            if (self::$saveBlocks === true) {
                $model->saveBlocks();
            }
        });

        static::deleted(function (Model $model) {
            if ($model->forceDeleting !== false) {
                $model->blocks()->detach();
            }
        });
    }

    /**
     * Get all of the blocks for this model instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function blocks()
    {
        $block = config('varbox.bindings.models.block_model', Block::class);

        return $this->morphToMany($block, 'blockable')->withPivot([
            'id', 'location', 'ord'
        ])->withTimestamps();
    }

    /**
     * Enable the url generation manually.
     *
     * @return static
     */
    public function doSaveBlocks()
    {
        self::$saveBlocks = false;

        return $this;
    }

    /**
     * Disable the url generation manually.
     *
     * @return static
     */
    public function doNotSaveBlocks()
    {
        self::$saveBlocks = false;

        return $this;
    }

    /**
     * Get all blocks assigned to this model instance from a given location in order.
     *
     * @param string $location
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBlocksInLocation($location)
    {
        return $this->blocks()->where('location', $location)->orderBy('ord', 'asc')->get();
    }

    /**
     * Get all blocks from database that can belong to the given location.
     *
     * @param string $location
     * @return \Illuminate\Support\Collection
     */
    public function getBlocksOfLocation($location)
    {
        $blocks = collect();

        foreach (app(BlockModelContract::class)->alphabetically()->get() as $block) {
            $types = (array)config('varbox.blocks.types', []);

            if (($class = $types[$block->type]['composer_class'] ?? null) && in_array($location, $class::$locations)) {
                $blocks->push($block);
            }
        }

        return $blocks;
    }

    /**
     * Get the inherited blocks for a model instance.
     * Inherited blocks can come from other model instances (recursively).
     *
     * @param string $location
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInheritedBlocks($location)
    {
        $this->initBlockOptions();

        if (!$this->blockOptions->inherit) {
            return $this->getBlocksInLocation($location);
        }

        $inheritor = null;

        if (is_string($this->blockOptions->inherit)) {
            if ($this->{$this->blockOptions->inherit} instanceof Model && $this->{$this->blockOptions->inherit}->exists) {
                $inheritor = $this->{$this->blockOptions->inherit};
            }
        } elseif ($this->blockOptions->inherit instanceof Model && $this->blockOptions->inherit->exists) {
            $inheritor = $this->blockOptions->inherit;
        }

        if ($inheritor instanceof Model) {
            $blocks = $inheritor->getBlocksInLocation($location);

            if ($blocks->count() > 0) {
                return $blocks;
            }

            if (
                is_string($inheritor->getBlockOptions()->inherit) &&
                $inheritor->{$inheritor->getBlockOptions()->inherit} instanceof Model &&
                $inheritor->{$inheritor->getBlockOptions()->inherit}->exists
            ) {
                return $inheritor->{$inheritor->getBlockOptions()->inherit}->getInheritedBlocks($location);
            }

            if (
                $inheritor->getBlockOptions()->inherit instanceof Model &&
                get_class($inheritor->getBlockOptions()->inherit) != get_class($this)
            ) {
                return $inheritor->getInheritedBlocks($location);
            }
        }

        return collect();
    }

    /**
     * Get all block locations for the given model instance.
     *
     * @return array|null
     */
    public function getBlockLocations()
    {
        $this->initBlockOptions();

        if (is_array($this->blockOptions->locations)) {
            return $this->blockOptions->locations;
        };

        return null;
    }

    /**
     * Get a list with all of the block locations currently assigned in database for this model instance.
     *
     * @return array
     */
    public function getExistingBlockLocations()
    {
        return $this->blocks()->newPivotStatement()->select('location')->where([
            'blockable_id' => $this->getKey(),
            'blockable_type' => static::class,
        ])->distinct()->get()->pluck('location')->toArray();
    }

    /**
     * Save all of the blocks of a model instance.
     * Saving is done on a provided or existing request object.
     * The logic of this method will look for the "blocks" key in the request.
     * Mandatory request format is an array of keys with their values composed of the block id and location.
     * [0 => [id => 1, location => header], 1 => [id => 1, location => footer]...]
     *
     * @param Request|null $request
     * @return bool
     */
    public function saveBlocks(Request $request = null)
    {
        $request = $request ?: request();
        $blocks = $request->input('blocks');

        DB::transaction(function () use ($blocks) {
            $this->blocks()->detach();

            if ($blocks && is_array($blocks) && !empty($blocks)) {
                ksort($blocks);

                foreach ($blocks as $data) {
                    foreach ($data as $id => $attributes) {
                        $block = app(BlockModelContract::class)->find($id);

                        if ($block && isset($attributes['location'])) {
                            $this->assignBlock($block, $attributes['location'], $attributes['ord'] ?? null);
                        }

                    }
                }
            }
        });

        return true;
    }

    /**
     * Assign a block to this model instance, matching the given location.
     *
     * @param BlockModelContract $block
     * @param string $location
     * @param int|null $order
     * @return bool
     */
    public function assignBlock(BlockModelContract $block, $location, $order = null)
    {
        if (!$order || !is_numeric($order)) {
            $order = 1;

            if ($last = $this->getBlocksInLocation($location)->last()) {
                if ($last->pivot && $last->pivot->ord) {
                    $order = $last->pivot->ord + 1;
                }
            }
        }

        $this->blocks()->save($block, [
            'location' => $location,
            'ord' => (int)$order
        ]);

        return true;
    }

    /**
     * Un-assign a block matching the pivot table id and location.
     * Delete the record from "blockables" table.
     *
     * @param BlockModelContract $block
     * @param string $location
     * @param int $pivot
     * @return bool
     */
    public function unassignBlock(BlockModelContract $block, $location, $pivot)
    {
        $this->blocks()
            ->newPivotStatementForId($block->getKey())
            ->where('location', $location)
            ->delete($pivot);

        return true;
    }

    /**
     * Sync a loaded model instance's assigned blocks from different locations with given locations.
     * The $locations parameter should represent the actual model instance's available block locations.
     *
     * @param array $locations
     * @return void
     */
    public function syncBlocks(array $locations = [])
    {
        foreach ($this->getExistingBlockLocations() as $location) {
            if (in_array($location, $locations)) {
                continue;
            }

            $this->blocks()->newPivotStatement()->where([
                'blockable_id' => $this->getKey(),
                'blockable_type' => static::class,
                'location' => $location,
            ])->delete();
        }
    }

    /**
     * Both instantiate the block options as well as validate their contents.
     *
     * @return void
     */
    protected function initBlockOptions()
    {
        if ($this->blockOptions === null) {
            $this->blockOptions = $this->getBlockOptions();
        }
    }
}
