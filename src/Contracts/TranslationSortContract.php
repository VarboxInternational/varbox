<?php

namespace Varbox\Contracts;

interface TranslationSortContract
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
