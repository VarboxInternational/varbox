<?php

namespace Varbox\Contracts;

interface PageSortContract
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
