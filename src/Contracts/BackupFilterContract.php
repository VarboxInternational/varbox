<?php

namespace Varbox\Contracts;

interface BackupFilterContract
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
