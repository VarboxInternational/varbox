<?php

namespace Varbox\Contracts;

interface BlockSortContract
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
