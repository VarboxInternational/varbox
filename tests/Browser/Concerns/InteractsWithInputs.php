<?php

namespace Varbox\Tests\Browser\Concerns;

trait InteractsWithInputs
{
    /**
     * Select an option inside a Select2.
     *
     * @param string $selector
     * @param string $value
     * @return $this
     */
    public function typeSelect2($selector, $value)
    {
        return $this->select2($selector, $value);
    }
}
