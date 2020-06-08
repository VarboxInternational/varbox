<?php

namespace Varbox\Contracts;

interface ActivityFilterContract
{
    /**
     * @return string
     */
    public function morph();

    /**
     * @return array
     */
    public function filters();

    /**
     * @return array
     */
    public function modifiers();
}
