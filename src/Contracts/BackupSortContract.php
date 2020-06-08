<?php

namespace Varbox\Contracts;

interface BackupSortContract
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
