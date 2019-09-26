<?php

namespace Varbox\Contracts;

interface SchemaModelContract
{
    /**
     * @return array
     */
    public function getTypes();

    /**
     * @return mixed
     */
    public function getTargetColumns();

    /**
     * @return array
     */
    public function articleSchemaTypes();
}
