<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RevisionHelperContract
{
/**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $route
     * @param array $parameters
     * @return \Illuminate\View\View
     */
    public function container(Model $model, $route, $parameters = []);

    /**
     * @param RevisionModelContract $revision
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\View\View
     */
    public function view(RevisionModelContract $revision, Model $model);
}
