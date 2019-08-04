<?php

namespace Varbox\Tests\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;

trait InteractsWithEditors
{
    /**
     * Type text inside a Froala editor.
     *
     * @param string $id
     * @param string $text
     * @return $this
     */
    public function froala($id, $text)
    {
        $froala = $this->driver->findElement(WebDriverBy::xpath(
            "//*[@id=\"" . $id . "\"]/preceding-sibling::div[contains(concat(' ',@class,' '),' fr-box ')]//div[contains(concat(' ',@class,' '),' fr-view ')]/p[1]"
        ));

        $this->driver->action()->moveToElement($froala)->sendKeys($froala, $text)->perform();

        return $this;
    }
}
