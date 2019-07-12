<?php

namespace Varbox\Tests\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase as Orchestra;

trait InteractsWithCrud
{
    /**
     * Visit the last page of a list.
     *
     * @param string $path
     * @param Model $model
     * @return $this
     */
    public function visitLastPage($path, Model $model)
    {
        $page = number_format(ceil(
            ($model->count() + 1) / config('varbox.crud.per_page', 10)
        ));

        $this->visit(rtrim($path, "/") . '/?page=' . $page);

        return $this;
    }

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

    /**
     * Assert that a page displays the correct records count.
     *
     * @param int $count
     * @return $this
     */
    public function assertRecordsCount($count)
    {
        Orchestra::assertCount($count, $this->driver->findElements(WebDriverBy::xpath(
            "//table[contains(concat(' ',@class,' '),' table ')]//tr[preceding-sibling::*]"
        )));

        return $this;
    }

    /**
     * Delete the record in list corresponding to the table row containing the specified text.
     *
     * @param string $text
     * @return $this
     */
    public function deleteRecord($text)
    {
        $this->clickDeleteButton($text)->whenAvailable('.bootbox-confirm', function ($modal) {
            $modal->assertSee('Are you sure?')->press('Yes');
        });

        return $this;
    }

    /**
     * Delete a record from a list by clicking any delete button
     *
     * @return $this
     */
    public function deleteAnyRecord()
    {
        $this->click('.button-delete')->whenAvailable('.bootbox-confirm', function ($modal) {
            $modal->assertSee('Are you sure?')->press('Yes');
        });

        return $this;
    }

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

        $this->select2($selector, $value)->press('Filter');

        return $this;
    }
}