<?php

namespace Varbox\Contracts;

interface AddressSortContract
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
