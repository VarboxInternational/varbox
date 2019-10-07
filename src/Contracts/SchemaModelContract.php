<?php

namespace Varbox\Contracts;

interface SchemaModelContract
{
    /**
     * @return array
     */
    public function schemaTypes();

    /**
     * @return mixed
     */
    public function getTargetColumns();

    /**
     * @return array
     */
    public function articleSchemaTypes();

    /**
     * @return array
     */
    public function eventSchemaTypes();

    /**
     * @return array
     */
    public function localBusinessSchemaTypes();

    /**
     * @return array
     */
    public function softwareApplicationSchemaTypes();
}
