<?php

namespace Varbox\Contracts;

interface ConfigModelContract
{
    /**
     * @return array|string
     */
    public function getValueAttribute();

    /**
     * @return array
     */
    public static function getAllowedKeys();
}
