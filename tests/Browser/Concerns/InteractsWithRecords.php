<?php

namespace Varbox\Tests\Browser\Concerns;

use Facebook\WebDriver\WebDriverBy;
use Illuminate\Database\Eloquent\Model;
use Orchestra\Testbench\TestCase as Orchestra;

trait InteractsWithRecords
{
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
     * Visit the last page of a list.
     *
     * @param string $path
     * @param Model $model
     * @return $this
     */
    public function visitLastPage($path, Model $model)
    {
        $perPage = config('varbox.crud.per_page', 10);

        if ($model->count() % $perPage == 0) {
            $perCount = $model->count();
        } else {
            $perCount = $model->count() + 1;
        }

        $page = number_format(ceil($perCount / $perPage));

        $this->visit(rtrim($path, "/") . '/?page=' . $page);

        return $this;
    }

    /**
     * Click the revisions container to open it.
     *
     * @return $this
     */
    public function openRevisionsContainer()
    {
        return $this->click('.revisions-list-header');
    }
}
