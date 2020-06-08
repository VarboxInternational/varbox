<?php

namespace Varbox\Contracts;

interface UploadSortContract
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
