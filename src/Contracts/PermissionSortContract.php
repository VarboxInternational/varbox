<?php

namespace Varbox\Contracts;

interface PermissionSortContract
{
    /**
     * @return string
     */
    public function field();

    /**
     * @return string
     */
    public function direction();
}
