<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface RevisionHelperContract
{
    /**
     * @param string $route
     * @param Model $model
     * @param RevisionModelContract|null $revision
     * @param array $parameters
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function container($route, Model $model, RevisionModelContract $revision = null, $parameters = []);
}
