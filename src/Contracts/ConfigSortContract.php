<?php

namespace Varbox\Contracts;

interface ConfigSortContract
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
