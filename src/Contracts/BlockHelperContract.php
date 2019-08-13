<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface BlockHelperContract
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $location
     * @return $this
     */
    public function setBlocksInLocation(Model $model, $location);

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBlocksInLocation();

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $location
     * @return $this
     */
    public function setInheritedBlocks(Model $model, $location);

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInheritedBlocks();

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $location
     * @param bool $inherits
     * @return null|void
     */
    public function render(Model $model, $location, $inherits = true);

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \Varbox\Contracts\RevisionModelContract $revision
     * @param bool $disabled
     * @return \Illuminate\View\View
     */
    public function container(Model $model, RevisionModelContract $revision = null, $disabled = false);
}
