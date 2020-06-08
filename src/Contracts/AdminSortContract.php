<?php

namespace Varbox\Contracts;

interface AdminSortContract
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
