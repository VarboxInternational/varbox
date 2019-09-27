<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface SchemaHelperContract
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return string
     */
    public function render(Model $model);
}
