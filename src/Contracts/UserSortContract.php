<?php

namespace Varbox\Contracts;

interface UserSortContract
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
