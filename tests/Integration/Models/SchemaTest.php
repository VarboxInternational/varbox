<?php

namespace Varbox\Tests\Integration\Models;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Varbox\Models\Schema;
use Varbox\Tests\Integration\TestCase;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class SchemaTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_uses_the_is_cacheable_trait()
    {
        $this->assertArrayHasKey(IsCacheable::class, class_uses(Schema::class));
    }

    /** @test */
    public function it_uses_the_is_filterable_trait()
    {
        $this->assertArrayHasKey(IsFilterable::class, class_uses(Schema::class));
    }

    /** @test */
    public function it_uses_the_is_sortable_trait()
    {
        $this->assertArrayHasKey(IsSortable::class, class_uses(Schema::class));
    }

    /** @test */
    public function it_can_return_the_available_schema_types()
    {
        $types = (new Schema)->schemaTypes();

        $this->assertArrayHasKey('article', $types);
        $this->assertArrayHasKey('product', $types);
        $this->assertArrayHasKey('event', $types);
        $this->assertArrayHasKey('person', $types);
        $this->assertArrayHasKey('book', $types);
        $this->assertArrayHasKey('review', $types);
        $this->assertArrayHasKey('course', $types);
        $this->assertArrayHasKey('job-posting', $types);
        $this->assertArrayHasKey('local-business', $types);
        $this->assertArrayHasKey('software-application', $types);
        $this->assertArrayHasKey('video-object', $types);
    }

    /** @test */
    public function it_can_return_article_schema_types()
    {
        $types = (new Schema)->articleSchemaTypes();

        $this->assertArrayHasKey('article', $types);
        $this->assertArrayHasKey('newsArticle', $types);
        $this->assertArrayHasKey('techArticle', $types);
        $this->assertArrayHasKey('scholarlyArticle', $types);
        $this->assertArrayHasKey('blogPosting', $types);
        $this->assertArrayHasKey('socialMediaPosting', $types);
        $this->assertArrayHasKey('report', $types);
    }

    /** @test */
    public function it_can_return_event_schema_types()
    {
        $types = (new Schema)->eventSchemaTypes();

        $this->assertArrayHasKey('event', $types);
        $this->assertArrayHasKey('businessEvent', $types);
        $this->assertArrayHasKey('childrensEvent', $types);
        $this->assertArrayHasKey('comedyEvent', $types);
        $this->assertArrayHasKey('courseInstance', $types);
        $this->assertArrayHasKey('danceEvent', $types);
        $this->assertArrayHasKey('deliveryEvent', $types);
        $this->assertArrayHasKey('educationEvent', $types);
        $this->assertArrayHasKey('exhibitionEvent', $types);
        $this->assertArrayHasKey('festival', $types);
        $this->assertArrayHasKey('foodEvent', $types);
        $this->assertArrayHasKey('literaryEvent', $types);
        $this->assertArrayHasKey('musicEvent', $types);
        $this->assertArrayHasKey('publicationEvent', $types);
        $this->assertArrayHasKey('saleEvent', $types);
        $this->assertArrayHasKey('screeningEvent', $types);
        $this->assertArrayHasKey('socialEvent', $types);
        $this->assertArrayHasKey('sportsEvent', $types);
        $this->assertArrayHasKey('theaterEvent', $types);
        $this->assertArrayHasKey('visualArtsEvent', $types);
    }

    /** @test */
    public function it_can_return_local_business_schema_types()
    {
        $types = (new Schema)->localBusinessSchemaTypes();

        $this->assertArrayHasKey('localBusiness', $types);
        $this->assertArrayHasKey('animalShelter', $types);
        $this->assertArrayHasKey('automotiveBusiness', $types);
        $this->assertArrayHasKey('childCare', $types);
        $this->assertArrayHasKey('dentist', $types);
        $this->assertArrayHasKey('dryCleaningOrLaundry', $types);
        $this->assertArrayHasKey('emergencyService', $types);
        $this->assertArrayHasKey('employmentAgency', $types);
        $this->assertArrayHasKey('entertainmentBusiness', $types);
        $this->assertArrayHasKey('financialService', $types);
        $this->assertArrayHasKey('foodEstablishment', $types);
        $this->assertArrayHasKey('governmentOffice', $types);
        $this->assertArrayHasKey('healthAndBeautyBusiness', $types);
        $this->assertArrayHasKey('homeAndConstructionBusiness', $types);
        $this->assertArrayHasKey('internetCafe', $types);
        $this->assertArrayHasKey('legalService', $types);
        $this->assertArrayHasKey('library', $types);
        $this->assertArrayHasKey('lodgingBusiness', $types);
        $this->assertArrayHasKey('medicalBusiness', $types);
        $this->assertArrayHasKey('professionalService', $types);
        $this->assertArrayHasKey('radioStation', $types);
        $this->assertArrayHasKey('realEstateAgent', $types);
        $this->assertArrayHasKey('recyclingCenter', $types);
        $this->assertArrayHasKey('selfStorage', $types);
        $this->assertArrayHasKey('shoppingCenter', $types);
        $this->assertArrayHasKey('sportsActivityLocation', $types);
        $this->assertArrayHasKey('store', $types);
        $this->assertArrayHasKey('televisionStation', $types);
        $this->assertArrayHasKey('touristInformationCenter', $types);
        $this->assertArrayHasKey('travelAgency', $types);
    }

    /** @test */
    public function it_can_return_software_application_schema_types()
    {
        $types = (new Schema)->softwareApplicationSchemaTypes();

        $this->assertArrayHasKey('softwareApplication', $types);
        $this->assertArrayHasKey('mobileApplication', $types);
        $this->assertArrayHasKey('webApplication', $types);
        $this->assertArrayHasKey('videoGame', $types);
    }
}
