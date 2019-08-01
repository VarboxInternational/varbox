<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface DraftHelperContract
{
    /**
     * @param string $route
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\View\View
     */
    public function container($route, Model $model);
}
