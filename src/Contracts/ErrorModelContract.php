<?php

namespace Varbox\Contracts;

interface ErrorModelContract
{
    /**
     * @param \Exception $exception
     * @return void
     */
    public function saveError(\Exception $exception);
}
