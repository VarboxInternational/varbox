<?php

namespace Varbox\Helpers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Varbox\Contracts\RevisionModelContract;
use Varbox\Contracts\BlockHelperContract;

class BlockHelper implements BlockHelperContract
{
    /**
     * The exiting blocks for a loaded model instance from a given location.
     *
     * @var Collection
     */
    protected $blocksInLocation;

    /**
     * The inherited blocks for a loaded model instance from a given location.
     *
     * @var Collection
     */
    protected $inheritedBlocks;

    /**
     * Set the $blocksInLocation property, only if not set.
     *
     * @param Model $model
     * @param string $location
     * @return $this
     */
    public function setBlocksInLocation(Model $model, $location)
    {
        if (!$this->blocksInLocation) {
            $this->blocksInLocation = $model->getBlocksInLocation($location);
        }

        return $this;
    }

    /**
     * Get the blocks from a location.
     *
     * @return Collection
     */
    public function getBlocksInLocation()
    {
        return $this->blocksInLocation;
    }

    /**
     * Set the $inheritedBlocks property, only if not set.
     *
     * @param Model $model
     * @param string $location
     * @return $this
     */
    public function setInheritedBlocks(Model $model, $location)
    {
        if (!$this->inheritedBlocks) {
            $this->inheritedBlocks = $model->getInheritedBlocks($location);
        }

        return $this;
    }

    /**
     * Get the inherited blocks.
     *
     * @return Collection
     */
    public function getInheritedBlocks()
    {
        return $this->inheritedBlocks;
    }

    /**
     * Render the blocks from a given location for a loaded model instance.
     * Inheriting functionality is also available.
     * If the model instance does not have any blocks assigned, but it inherits blocks, those will be rendered.
     *
     * @param Model $model
     * @param string $location
     * @param bool $inherits
     * @return null|void
     */
    public function render(Model $model, $location, $inherits = true)
    {
        if (!$model->exists) {
            return null;
        }

        $this->setBlocksInLocation($model, $location);

        if ($this->getBlocksInLocation()->count() > 0) {
            foreach ($this->getBlocksInLocation() as $block) {
                echo view()->make("blocks_{$block->type}::front")->with([
                    'model' => $block
                ])->render();
            }

            return;
        }

        if ($inherits === true) {
            $this->setInheritedBlocks($model, $location);

            if ($this->getInheritedBlocks()->count() > 0) {
                foreach ($this->getInheritedBlocks() as $block) {
                    echo view()->make("blocks_{$block->type}::front")->with([
                        'model' => $block
                    ])->render();
                }

                return;
            }
        }

        return null;
    }

    /**
     * Build the block containers html.
     *
     * @param Model $model
     * @param RevisionModelContract $revision
     * @return \Illuminate\View\View
     */
    public function container(Model $model, RevisionModelContract $revision = null)
    {
        return view('varbox::helpers.block.container')->with([
            'model' => $model,
            'revision' => $revision,
            'disabled' => $revision instanceof RevisionModelContract,
        ]);
    }
}
