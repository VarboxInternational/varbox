<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface SchemaHelperContract
{
    /**
     * @param SchemaModelContract $schema
     * @param Model $model
     * @return string|void
     */
    public function renderSingle(SchemaModelContract $schema, Model $model);

    /**
     * @param Model $model
     * @return string
     */
    public function renderAll(Model $model);
}
