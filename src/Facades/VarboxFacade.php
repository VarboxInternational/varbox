<?php

namespace Varbox\Facades;

use Illuminate\Support\Facades\Facade;

class VarboxFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'varbox';
    }
}
