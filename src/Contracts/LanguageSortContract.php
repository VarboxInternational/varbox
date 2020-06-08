<?php

namespace Varbox\Contracts;

interface LanguageSortContract
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
