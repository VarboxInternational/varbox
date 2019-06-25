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
            ($model->count() + 1) / config('varbox.varbox-crud.per_page', 10)
        ));

        $this->visit(rtrim($path, "/") . '/?page=' . $page);

        return $this;
    }

    /**
     * Click the edit button of table row containing the specified text.
     *
     * @param string
     * @return $this
     */
    public function clickEditButton($text)
    {
        $this->driver->findElement(
            WebDriverBy::xpath(
                "//td[contains(text(),'$text')]//following-sibling::td[last()]//a[contains(concat(' ',@class,' '),' button-edit ')]"
            )
        )->click();

        return $this;
    }

    /**
     * Click the delete button of table row containing the specified text.
     *
     * @param string
     * @return $this
     */
    public function clickDeleteButton($text)
    {
        $this->driver->findElement(
            WebDriverBy::xpath(
                "//td[contains(text(),'$text')]//following-sibling::td[last()]//button[contains(concat(' ',@class,' '),' button-delete ')]"
            )
        )->click();

        return $this;
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
     * @return $this
     */
    public function filterRecordsByText($selector, $text)
    {
        $this->click('.filter-records-container')->type($selector, $text)->press('Filter');

        return $this;
    }

    /**
     * Filter the records by interacting with a select input.
     *
     * @param string $selector
     * @param string $value
     * @return $this
     */
    public function filterRecordsBySelect($selector, $value)
    {
        $this->click('.filter-records-container')->select2($selector, $value)->press('Filter');

        return $this;
    }
}