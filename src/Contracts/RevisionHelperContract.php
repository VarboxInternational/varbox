<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RevisionHelperContract
{
    /**
     * @param Model $model
     * @param string $route
     * @param RevisionModelContract|null $revision
     * @param array $parameters
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function container(Model $model, $route, RevisionModelContract $revision = null, $parameters = []);
}
