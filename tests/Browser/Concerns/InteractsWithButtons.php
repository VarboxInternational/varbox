<?php

namespace Varbox\Tests\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;

trait InteractsWithButtons
{
    /**
     * Click the edit button of table row containing the specified text.
     *
     * @param string $text
     * @param string $class
     * @param string $element
     * @return $this
     */
    public function clickButton($text, $class, $element = 'button')
    {
        $this->driver->findElement(
            WebDriverBy::xpath(
                "//td[contains(.,'{$text}')]//following-sibling::td[last()]//{$element}[contains(concat(' ',@class,' '),' {$class} ')]"
            )
        )->click();

        return $this;
    }

    /**
     * Click the edit button of table row containing the specified text.
     *
     * @param string $text
     * @param string $element
     * @return $this
     */
    public function clickButtonWithConfirm($text, $element = 'button')
    {
        return $this->clickLink($text, $element)->whenAvailable('.bootbox-confirm', function ($modal) {
            $modal->assertSee('Are you sure?')->press('Yes');
        });
    }

    /**
     * Click the edit button of table row containing the specified text.
     *
     * @param string $text
     * @return $this
     */
    public function clickEditButton($text)
    {
        return $this->clickButton($text, 'button-edit', 'a');
    }

    /**
     * Click the edit button of table row containing the specified text.
     *
     * @param string $text
     * @return $this
     */
    public function clickViewButton($text)
    {
        return $this->clickButton($text, 'button-view', 'a');
    }

    /**
     * Click the delete button of table row containing the specified text.
     *
     * @param string $text
     * @return $this
     */
    public function clickDeleteButton($text)
    {
        return $this->clickButton($text, 'button-delete');
    }
}
