<?php

namespace Varbox\Contracts;

interface CitySortContract
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
