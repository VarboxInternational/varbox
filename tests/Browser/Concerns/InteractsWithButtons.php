<?php

namespace Varbox\Tests\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;

trait InteractsWithButtons
{
    /**
     * Click a button of table row containing the specified text.
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
     * Click a button of table row containing the specified text and confirm the popup.
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
     * Click a button and confirm the popup.
     *
     * @param $selector
     * @return $this
     */
    public function clickButtonWithConfirmBySelector($selector)
    {
        return $this->click($selector)->whenAvailable('.bootbox-confirm', function ($modal) {
            $modal->assertSee('Are you sure?')->press('Yes');
        });
    }

    /**
     * Press a submit button and confirm popup.
     *
     * @param string $text
     * @return $this
     */
    public function pressButtonWithConfirm($text)
    {
        return $this->press($text)->whenAvailable('.bootbox-confirm', function ($modal) {
            $modal->assertSee('Are you sure?')->press('Yes');
        });
    }

    /**
     * Click the edit record button.
     *
     * @param string $text
     * @return $this
     */
    public function clickEditRecordButton($text)
    {
        return $this->clickButton($text, 'button-edit', 'a');
    }

    /**
     * Click the view record button.
     *
     * @param string $text
     * @return $this
     */
    public function clickViewRecordButton($text)
    {
        return $this->clickButton($text, 'button-view', 'a');
    }

    /**
     * Click the delete record button.
     *
     * @param string $text
     * @return $this
     */
    public function clickDeleteRecordButton($text)
    {
        return $this->clickButton($text, 'button-delete')
            ->whenAvailable('.bootbox-confirm', function ($modal) {
                $modal->assertSee('Are you sure?')->press('Yes');
            });
    }

    /**
     * Click any delete record button.
     *
     * @return $this
     */
    public function clickDeleteAnyRecordButton()
    {
        return $this->click('.button-delete')
            ->whenAvailable('.bootbox-confirm', function ($modal) {
                $modal->assertSee('Are you sure?')->press('Yes');
            });
    }

    /**
     * Click the restore record button.
     *
     * @param string $text
     * @return $this
     */
    public function clickRestoreRecordButton($text)
    {
        return $this->clickButton($text, 'button-restore')
            ->whenAvailable('.bootbox-confirm', function ($modal) {
                $modal->assertSee('Are you sure?')->press('Yes');
            });
    }

    /**
     * Click the save record as draft button.
     *
     * @return $this
     */
    public function clickSaveDraftRecordButton()
    {
        return $this->clickButtonWithConfirm('Save As Draft', 'a');
    }

    /**
     * Click the publish record button.
     *
     * @return $this
     */
    public function clickPublishRecordButton()
    {
        return $this->clickButtonWithConfirm('Publish', 'button');
    }

    /**
     * Click the duplicate record button.
     *
     * @return $this
     */
    public function clickDuplicateRecordButton()
    {
        return $this->clickButtonWithConfirm('Duplicate', 'a');
    }

    /**
     * Click the view revision button.
     *
     * @return $this
     */
    public function clickViewRevisionButton()
    {
        return $this->click('.button-view-revision');
    }

    /**
     * Click the rollback revision button.
     *
     * @return $this
     */
    public function clickRollbackRevisionButton()
    {
        return $this->clickButtonWithConfirmBySelector('.button-rollback-revision');
    }

    /**
     * Press the rollback revision button.
     *
     * @return $this
     */
    public function pressRollbackRevisionButton()
    {
        return $this->pressButtonWithConfirm('Rollback Revision');
    }

    /**
     * Click the delete revision button.
     *
     * @return $this
     */
    public function clickDeleteRevisionButton()
    {
        return $this->clickButtonWithConfirmBySelector('.button-delete-revision');
    }
}
