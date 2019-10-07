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
    const TYPE_JOB_POSTING = 'job-posting';
    const TYPE_LOCAL_BUSINESS = 'local-business';
    const TYPE_SOFTWARE_APPLICATION = 'software-application';
    const TYPE_VIDEO_OBJECT = 'video-object';

    /**
     * Get all available Schema types.
     *
     * @return array
     */
    public function schemaTypes()
    {
        return [
            static::TYPE_ARTICLE => 'Article',
            static::TYPE_PRODUCT => 'Product',
            static::TYPE_EVENT => 'Event',
            static::TYPE_PERSON => 'Person',
            static::TYPE_BOOK => 'Book',
            static::TYPE_REVIEW => 'Review',
            static::TYPE_COURSE => 'Course',
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
            'article' => 'Article',
            'newsArticle' => 'News Article',
            'techArticle' => 'Tech Article',
            'scholarlyArticle' => 'Scholarly Article',
            'blogPosting' => 'Blog Posting',
            'socialMediaPosting' => 'Social Media Posting',
            'report' => 'Report',
        ];
    }

    /**
     * Get the more specific schema.org event types.
     *
     * @return array
     */
    public function eventSchemaTypes()
    {
        return [
            'event' => 'Event',
            'businessEvent' => 'Business Event',
            'childrensEvent' => 'Children\'s Event',
            'comedyEvent' => 'Comedy Event',
            'courseInstance' => 'Course Instance',
            'danceEvent' => 'Dance Event',
            'deliveryEvent' => 'Delivery Event',
            'educationEvent' => 'Education Event',
            'exhibitionEvent' => 'Exhibition Event',
            'festival' => 'Festival',
            'foodEvent' => 'Food Event',
            'literaryEvent' => 'Literary Event',
            'musicEvent' => 'Music Event',
            'publicationEvent' => 'Publication Event',
            'saleEvent' => 'Sale Event',
            'screeningEvent' => 'Screening Event',
            'socialEvent' => 'Social Event',
            'sportsEvent' => 'Sports Event',
            'theaterEvent' => 'Theater Event',
            'visualArtsEvent' => 'Visual Arts Event',
        ];
    }

    /**
     * Get the more specific schema.org event types.
     *
     * @return array
     */
    public function localBusinessSchemaTypes()
    {
        return [
            'localBusiness' => 'Local Business',
            'animalShelter' => 'Animal Shelter',
            'automotiveBusiness' => 'Automotive Business',
            'childCare' => 'Child Care',
            'dentist' => 'Dentist',
            'dryCleaningOrLaundry' => 'Dry Cleaning Or Laundry',
            'emergencyService' => 'Emergency Service',
            'employmentAgency' => 'Employment Agency',
            'entertainmentBusiness' => 'Entertainment Business',
            'financialService' => 'Financial Service',
            'foodEstablishment' => 'Food Establishment',
            'governmentOffice' => 'Government Office',
            'healthAndBeautyBusiness' => 'Health & Beauty Business',
            'homeAndConstructionBusiness' => 'Home & Construction Business',
            'internetCafe' => 'Internet Cafe',
            'legalService' => 'Legal Service',
            'library' => 'Library',
            'lodgingBusiness' => 'Lodging Business',
            'medicalBusiness' => 'Medical Business',
            'professionalService' => 'Professional Service',
            'radioStation' => 'Radio Station',
            'realEstateAgent' => 'Real Estate Agent',
            'recyclingCenter' => 'Recycling Center',
            'selfStorage' => 'Self Storage',
            'shoppingCenter' => 'Shopping Center',
            'sportsActivityLocation' => 'Sports Activity Location',
            'store' => 'Store',
            'televisionStation' => 'Television Station',
            'touristInformationCenter' => 'Tourist Information Center',
            'travelAgency' => 'Travel Agency',
        ];
    }

    /**
     * Get the more specific schema.org event types.
     *
     * @return array
     */
    public function softwareApplicationSchemaTypes()
    {
        return [
            'softwareApplication' => 'Software Application',
            'mobileApplication' => 'Mobile Application',
            'webApplication' => 'Web Application',
            'videoGame' => 'Video Game',
        ];
    }
}
