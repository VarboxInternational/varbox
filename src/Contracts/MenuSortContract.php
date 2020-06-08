<?php

namespace Varbox\Contracts;

interface MenuSortContract
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
