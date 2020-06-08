<?php

namespace Varbox\Contracts;

interface ErrorSortContract
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
