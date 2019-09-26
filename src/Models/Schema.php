<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema as SchemaFacade;
use Varbox\Contracts\SchemaModelContract;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class Schema extends Model implements SchemaModelContract
{
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'schema';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'target',
        'fields',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'fields' => 'array',
    ];

    /**
     * The available Schema types.
     *
     * @const
     */
    const TYPE_ARTICLE = 'article';
    const TYPE_PRODUCT = 'product';
    const TYPE_EVENT = 'event';
    const TYPE_PERSON = 'person';
    const TYPE_BOOK = 'book';
    const TYPE_REVIEW = 'review';
    const TYPE_COURSE = 'course';
    const TYPE_RECIPE = 'recipe';
    const TYPE_SERVICE = 'service';
    const TYPE_JOB_POSTING = 'job-posting';
    const TYPE_LOCAL_BUSINESS = 'local-business';
    const TYPE_SOFTWARE_APPLICATION = 'software-application';
    const TYPE_VIDEO_OBJECT = 'video-object';

    /**
     * Get all available Schema types.
     *
     * @return array
     */
    public function getTypes()
    {
        return [
            static::TYPE_ARTICLE => 'Article',
            static::TYPE_PRODUCT => 'Product',
            static::TYPE_EVENT => 'Event',
            static::TYPE_PERSON => 'Person',
            static::TYPE_BOOK => 'Book',
            static::TYPE_REVIEW => 'Review',
            static::TYPE_COURSE => 'Course',
            static::TYPE_RECIPE => 'Recipe',
            static::TYPE_SERVICE => 'Service',
            static::TYPE_JOB_POSTING => 'Job Posting',
            static::TYPE_LOCAL_BUSINESS => 'Local Business',
            static::TYPE_SOFTWARE_APPLICATION => 'Software Application',
            static::TYPE_VIDEO_OBJECT => 'Video Object',
        ];
    }

    /**
     * Get all table columns from the model that's applying Schema.
     *
     * @return mixed
     */
    public function getTargetColumns()
    {
        $model = app($this->target);
        $table = $model->getTable();

        return SchemaFacade::getColumnListing($table);
    }

    /**
     * Get the more specific schema.org article types.
     *
     * @return array
     */
    public function articleSchemaTypes()
    {
        return [
            'newsArticle' => 'News Article',
            'techArticle' => 'Tech Article',
            'scholarlyArticle' => 'Scholarly Article',
            'blogPosting' => 'Blog Posting',
            'socialMediaPosting' => 'Social Media Posting',
            'report' => 'Report',
        ];
    }
}
