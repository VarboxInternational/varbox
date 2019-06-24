<?php

namespace Varbox\Contracts;

interface ValidationHelperContract
{
    /**
     * @param string|null $type
     */
    public function __construct($type = null);

    /**
     * @return \Illuminate\View\View
     */
    public function errors();
}
