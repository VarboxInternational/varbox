<?php

namespace Varbox\Helpers;

use Illuminate\Database\Eloquent\Model;
use Varbox\Contracts\SchemaHelperContract;
use Varbox\Contracts\SchemaModelContract;
use Varbox\Schema\Article;
use Varbox\Schema\Book;
use Varbox\Schema\Course;
use Varbox\Schema\Event;
use Varbox\Schema\JobPosting;
use Varbox\Schema\LocalBusiness;
use Varbox\Schema\Person;
use Varbox\Schema\Product;
use Varbox\Schema\Review;
use Varbox\Schema\SoftwareApplication;
use Varbox\Schema\VideoObject;

class SchemaHelper implements SchemaHelperContract
{
    /**
     * @var array
     */
    protected $output = [];

    /**
     * Display the generated json+ld schema code for a model.
     *
     * @param Model $model
     * @return string
     */
    public function render(Model $model)
    {
        $class = app(SchemaModelContract::class);
        $schemas = $class::whereTarget($model->getMorphClass())->get();

        foreach ($schemas as $schema) {
            switch ($schema->type) {
                case $class::TYPE_ARTICLE:
                    $this->output[] = (new Article($model, $schema))->generate();
                    break;
                case $class::TYPE_PRODUCT:
                    $this->output[] = (new Product($model, $schema))->generate();
                    break;
                case $class::TYPE_EVENT:
                    $this->output[] = (new Event($model, $schema))->generate();
                    break;
                case $class::TYPE_PERSON:
                    $this->output[] = (new Person($model, $schema))->generate();
                    break;
                case $class::TYPE_BOOK:
                    $this->output[] = (new Book($model, $schema))->generate();
                    break;
                case $class::TYPE_COURSE:
                    $this->output[] = (new Course($model, $schema))->generate();
                    break;
                case $class::TYPE_JOB_POSTING:
                    $this->output[] = (new JobPosting($model, $schema))->generate();
                    break;
                case $class::TYPE_LOCAL_BUSINESS:
                    $this->output[] = (new LocalBusiness($model, $schema))->generate();
                    break;
                case $class::TYPE_SOFTWARE_APPLICATION:
                    $this->output[] = (new SoftwareApplication($model, $schema))->generate();
                    break;
                case $class::TYPE_VIDEO_OBJECT:
                    $this->output[] = (new VideoObject($model, $schema))->generate();
                    break;
                case $class::TYPE_REVIEW:
                    $this->output[] = (new Review($model, $schema))->generate();
                    break;
            }
        }

        return implode('', $this->output);
    }
}
