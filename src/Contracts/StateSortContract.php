<?php

namespace Varbox\Contracts;

interface StateSortContract
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
