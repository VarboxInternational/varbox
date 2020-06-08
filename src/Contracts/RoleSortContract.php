<?php

namespace Varbox\Contracts;

interface RoleSortContract
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
