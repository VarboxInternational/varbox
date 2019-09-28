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
     * Display the generated json+ld schema code for the specified schema and model.
     *
     * @param SchemaModelContract $schema
     * @param Model $model
     * @return string|void
     */
    public function renderSingle(SchemaModelContract $schema, Model $model)
    {
        switch ($schema->type) {
            case $schema::TYPE_ARTICLE:
                return (new Article($model, $schema))->generate();
                break;
            case $schema::TYPE_PRODUCT:
                return (new Product($model, $schema))->generate();
                break;
            case $schema::TYPE_EVENT:
                return (new Event($model, $schema))->generate();
                break;
            case $schema::TYPE_PERSON:
                return (new Person($model, $schema))->generate();
                break;
            case $schema::TYPE_BOOK:
                return (new Book($model, $schema))->generate();
                break;
            case $schema::TYPE_COURSE:
                return (new Course($model, $schema))->generate();
                break;
            case $schema::TYPE_JOB_POSTING:
                return (new JobPosting($model, $schema))->generate();
                break;
            case $schema::TYPE_LOCAL_BUSINESS:
                return (new LocalBusiness($model, $schema))->generate();
                break;
            case $schema::TYPE_SOFTWARE_APPLICATION:
                return (new SoftwareApplication($model, $schema))->generate();
                break;
            case $schema::TYPE_VIDEO_OBJECT:
                return (new VideoObject($model, $schema))->generate();
                break;
            case $schema::TYPE_REVIEW:
                return (new Review($model, $schema))->generate();
                break;
        }
    }

    /**
     * Display the generated json+ld schema code for a model.
     *
     * @param Model $model
     * @return string
     */
    public function renderAll(Model $model)
    {
        $schemas = app(SchemaModelContract::class)
            ->whereTarget($model->getMorphClass())
            ->get();

        foreach ($schemas as $schema) {
            $this->output[] = $this->renderSingle($schema, $model);
        }

        return implode('', $this->output);
    }
}
