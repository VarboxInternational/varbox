<?php

namespace Varbox\Tests\Browser\Concerns;

trait InteractsWithFilters
{
    /**
     * Filter the records by interacting with a text input.
     *
     * @param string $selector
     * @param string $text
     * @param bool $opened
     * @return $this
     */
    public function filterRecordsByText($selector, $text, $opened = false)
    {
        if ($opened === false) {
            $this->click('.filter-records-container')->waitForText('Filter');
        }

        $this->type($selector, $text)->press('Filter');

        return $this;
    }

    /**
     * Filter the records by interacting with a select input.
     *
     * @param string $selector
     * @param string $value
     * @param bool $opened
     * @return $this
     */
    public function filterRecordsBySelect($selector, $value, $opened = false)
    {
        if ($opened === false) {
            $this->click('.filter-records-container')->waitForText('Filter');
        }

        $this->typeSelect2($selector, $value)->press('Filter');

        return $this;
    }
}
