<?php

namespace Varbox\Tests\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;

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

    /**
     * Type text inside a Froala editor.
     *
     * @param string $id
     * @param string $text
     * @return $this
     */
    public function typeFroala($id, $value)
    {
        $froala = $this->driver->findElement(WebDriverBy::xpath(
            "//*[@id=\"" . $id . "\"]/preceding-sibling::div[contains(concat(' ',@class,' '),' fr-box ')]//div[contains(concat(' ',@class,' '),' fr-view ')]/p[1]"
        ));

        $this->driver->action()->moveToElement($froala)->sendKeys($froala, $value)->perform();

        return $this;
    }
}
