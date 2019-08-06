<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface DraftHelperContract
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $route
     * @param string|null $permission
     * @return \Illuminate\View\View
     */
    public function container(Model $model, $route, $permission = null);
}
