<?php

namespace Varbox\Tests\Browser;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Varbox\Models\Schema;
use Varbox\Models\User;

class SchemaTest extends TestCase
{
    /**
     * @var Schema
     */
    protected $schemaModel;

    /**
     * @var string
     */
    protected $schemaName = 'Test Schema Name';
    protected $schemaTarget = 'All Users';

    /**
     * @var string
     */
    protected $schemaNameModified = 'Test Schema Name Modified';

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('varbox.schema.targets', [
            User::class => 'All Users'
        ]);
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->assertPathIs('/admin/schema')
                ->assertSee('Schema');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('schema-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->assertPathIs('/admin/schema')
                ->assertSee('Schema');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('schema-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->assertSee('Unauthorized')
                ->assertDontSee('Schema');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertPathIs('/admin/schema/create')
                ->assertSee('Add Schema');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertPathIs('/admin/schema/create')
                ->assertSee('Add Schema');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->revokePermission('schema-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->assertDontSee('Add New')
                ->visit('/admin/schema/create')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Schema');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createSchema();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/schema', $this->schemaModel)
                ->clickEditRecordButton($this->schemaName)
                ->assertPathIs('/admin/schema/edit/' . $this->schemaModel->id)
                ->assertSee('Edit Schema');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-edit');

        $this->createSchema();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/schema', $this->schemaModel)
                ->clickEditRecordButton($this->schemaName)
                ->assertPathIs('/admin/schema/edit/' . $this->schemaModel->id)
                ->assertSee('Edit Schema');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->revokePermission('schema-edit');

        $this->createSchema();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/schema', $this->schemaModel)
                ->assertSourceMissing('button-edit')
                ->visit('/admin/schema/edit/' . $this->schemaModel->id)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Schema');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_add_a_schema_of_type_article()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');
        $this->admin->grantPermission('schema-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertSee('Article')
                ->clickLink('Article')
                ->assertPathIs('/admin/schema/create/article')
                ->type('#name-input', $this->schemaName)
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->clickLink('Continue')
                ->assertPathBeginsWith('/admin/schema/edit/')
                ->assertSee('Article Type')
                ->assertSee('Headline')
                ->assertSee('Description')
                ->assertSee('Image')
                ->assertSee('Date Published')
                ->assertSee('Date Modified')
                ->assertSee('Author Name')
                ->assertSee('Publisher Name')
                ->assertSee('Publisher Logo');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_add_a_schema_of_type_product()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');
        $this->admin->grantPermission('schema-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertSee('Product')
                ->clickLink('Product')
                ->assertPathIs('/admin/schema/create/product')
                ->type('#name-input', $this->schemaName)
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->clickLink('Continue')
                ->assertPathBeginsWith('/admin/schema/edit/')
                ->assertSee('Name')
                ->assertSee('Description')
                ->assertSee('Image')
                ->assertSee('Url')
                ->assertSee('Price')
                ->assertSee('Currency')
                ->assertSee('Stock')
                ->assertSee('Brand')
                ->assertSee('Sku')
                ->assertSee('Isbn / Mpn / Gtin8')
                ->assertSee('Rating')
                ->assertSee('Review Count');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_add_a_schema_of_type_event()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');
        $this->admin->grantPermission('schema-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertSee('Event')
                ->clickLink('Event')
                ->assertPathIs('/admin/schema/create/event')
                ->type('#name-input', $this->schemaName)
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->clickLink('Continue')
                ->assertPathBeginsWith('/admin/schema/edit/')
                ->assertSee('Event Type')
                ->assertSee('Name')
                ->assertSee('Url')
                ->assertSee('Description')
                ->assertSee('Image')
                ->assertSee('Start Date')
                ->assertSee('End Date')
                ->assertSee('Performer Name')
                ->assertSee('Location Name')
                ->assertSee('Street Address')
                ->assertSee('Postal Code')
                ->assertSee('City')
                ->assertSee('Region')
                ->assertSee('Country')
                ->assertSee('Price')
                ->assertSee('Currency')
                ->assertSee('Stock');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_add_a_schema_of_type_person()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');
        $this->admin->grantPermission('schema-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertSee('Person')
                ->clickLink('Person')
                ->assertPathIs('/admin/schema/create/person')
                ->type('#name-input', $this->schemaName)
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->clickLink('Continue')
                ->assertPathBeginsWith('/admin/schema/edit/')
                ->assertSee('Name')
                ->assertSee('Url')
                ->assertSee('Email')
                ->assertSee('Phone')
                ->assertSee('Description')
                ->assertSee('Image')
                ->assertSee('Job Title')
                ->assertSee('Work Place')
                ->assertSee('College Graduated')
                ->assertSee('Height')
                ->assertSee('Weight')
                ->assertSee('Gender')
                ->assertSee('Personal Site')
                ->assertSee('Wikipedia Page')
                ->assertSee('Facebook Profile')
                ->assertSee('Twitter Profile')
                ->assertSee('Linkedin Profile')
                ->assertSee('Google+ Profile')
                ->assertSee('Youtube Profile')
                ->assertSee('Instagram Profile')
                ->assertSee('Address City')
                ->assertSee('Address Region')
                ->assertSee('Address Country')
                ->assertSee('Birth City')
                ->assertSee('Birth Region')
                ->assertSee('Birth Country');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_add_a_schema_of_type_book()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');
        $this->admin->grantPermission('schema-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertSee('Book')
                ->clickLink('Book')
                ->assertPathIs('/admin/schema/create/book')
                ->type('#name-input', $this->schemaName)
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->clickLink('Continue')
                ->assertPathBeginsWith('/admin/schema/edit/')
                ->assertSee('Name')
                ->assertSee('Description')
                ->assertSee('Image')
                ->assertSee('Url')
                ->assertSee('Genre')
                ->assertSee('Number of Pages')
                ->assertSee('Author Name')
                ->assertSee('Publisher Name')
                ->assertSee('Price')
                ->assertSee('Currency')
                ->assertSee('Rating')
                ->assertSee('Review Count');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_add_a_schema_of_type_review()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');
        $this->admin->grantPermission('schema-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertSee('Review')
                ->clickLink('Review')
                ->assertPathIs('/admin/schema/create/review')
                ->type('#name-input', $this->schemaName)
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->clickLink('Continue')
                ->assertPathBeginsWith('/admin/schema/edit/')
                ->assertSee('Name')
                ->assertSee('Image')
                ->assertSee('Description')
                ->assertSee('Rating');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_add_a_schema_of_type_course()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');
        $this->admin->grantPermission('schema-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertSee('Course')
                ->clickLink('Course')
                ->assertPathIs('/admin/schema/create/course')
                ->type('#name-input', $this->schemaName)
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->clickLink('Continue')
                ->assertPathBeginsWith('/admin/schema/edit/')
                ->assertSee('Name')
                ->assertSee('Description')
                ->assertSee('Image')
                ->assertSee('Url')
                ->assertSee('Start Date')
                ->assertSee('End Date')
                ->assertSee('Price')
                ->assertSee('Currency')
                ->assertSee('Stock')
                ->assertSee('Provider Name')
                ->assertSee('Performer Name')
                ->assertSee('Location Name')
                ->assertSee('Street Address')
                ->assertSee('Postal Code')
                ->assertSee('City')
                ->assertSee('Region')
                ->assertSee('Country')
                ->assertSee('Rating')
                ->assertSee('Review Count');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_add_a_schema_of_type_job_posting()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');
        $this->admin->grantPermission('schema-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertSee('Job Posting')
                ->clickLink('Job Posting')
                ->assertPathIs('/admin/schema/create/job-posting')
                ->type('#name-input', $this->schemaName)
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->clickLink('Continue')
                ->assertPathBeginsWith('/admin/schema/edit/')
                ->assertSee('Title')
                ->assertSee('Description')
                ->assertSee('Base Salary')
                ->assertSee('Salary Currency')
                ->assertSee('Salary Unit')
                ->assertSee('Employment Type')
                ->assertSee('Date Posted')
                ->assertSee('Valid Until')
                ->assertSee('Organization Name')
                ->assertSee('Organization Logo')
                ->assertSee('City')
                ->assertSee('Region')
                ->assertSee('Country')
                ->assertSee('Street Address')
                ->assertSee('Postal Code');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_add_a_schema_of_type_local_business()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');
        $this->admin->grantPermission('schema-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertSee('Local Business')
                ->clickLink('Local Business')
                ->assertPathIs('/admin/schema/create/local-business')
                ->type('#name-input', $this->schemaName)
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->clickLink('Continue')
                ->assertPathBeginsWith('/admin/schema/edit/')
                ->assertSee('Local Business Type')
                ->assertSee('Name')
                ->assertSee('Url')
                ->assertSee('Description')
                ->assertSee('Image')
                ->assertSee('Telephone')
                ->assertSee('Price Range')
                ->assertSee('Country Address')
                ->assertSee('City Address')
                ->assertSee('Street Address')
                ->assertSee('Postal Code')
                ->assertSee('Latitude')
                ->assertSee('Longitude')
                ->assertSee('Monday Opens At')
                ->assertSee('Monday Closes At')
                ->assertSee('Tuesday Opens At')
                ->assertSee('Tuesday Closes At')
                ->assertSee('Wednesday Opens At')
                ->assertSee('Wednesday Closes At')
                ->assertSee('Thursday Opens At')
                ->assertSee('Thursday Closes At')
                ->assertSee('Friday Opens At')
                ->assertSee('Friday Closes At')
                ->assertSee('Saturday Opens At')
                ->assertSee('Saturday Closes At')
                ->assertSee('Sunday Opens At')
                ->assertSee('Sunday Closes At')
                ->assertSee('Rating')
                ->assertSee('Review Count');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_add_a_schema_of_type_software_application()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');
        $this->admin->grantPermission('schema-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertSee('Software Application')
                ->clickLink('Software Application')
                ->assertPathIs('/admin/schema/create/software-application')
                ->type('#name-input', $this->schemaName)
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->clickLink('Continue')
                ->assertPathBeginsWith('/admin/schema/edit/')
                ->assertSee('Event Type')
                ->assertSee('Name')
                ->assertSee('Category')
                ->assertSee('Image')
                ->assertSee('Operating System')
                ->assertSee('Price')
                ->assertSee('Currency')
                ->assertSee('Rating')
                ->assertSee('Review Count');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_add_a_schema_of_type_video_object()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');
        $this->admin->grantPermission('schema-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->clickLink('Add New')
                ->assertSee('Video Object')
                ->clickLink('Video Object')
                ->assertPathIs('/admin/schema/create/video-object')
                ->type('#name-input', $this->schemaName)
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->clickLink('Continue')
                ->assertPathBeginsWith('/admin/schema/edit/')
                ->assertSee('Name')
                ->assertSee('Description')
                ->assertSee('Upload Date')
                ->assertSee('File Url')
                ->assertSee('Embed Url')
                ->assertSee('Thumbnail Url');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_update_a_schema()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-edit');

        $this->createSchema();

        $this->browse(function ($browser) {
            $browser->resize(1200, 1200)->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/schema', $this->schemaModel)
                ->clickEditRecordButton($this->schemaName)
                ->type('#name-input', $this->schemaNameModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/schema')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/schema', $this->schemaModel)
                ->assertSee($this->schemaNameModified);
        });

        $this->deleteSchemaModified();
    }

    /** @test */
    public function an_admin_can_update_a_schema_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-edit');

        $this->createSchema();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/schema', $this->schemaModel)
                ->clickEditRecordButton($this->schemaName)
                ->type('#name-input', $this->schemaNameModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/schema/edit/' . $this->schemaModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#name-input', $this->schemaNameModified);
        });

        $this->deleteSchemaModified();
    }

    /** @test */
    public function an_admin_can_delete_a_schema_if_it_has_permission()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-delete');

        $this->createSchema();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/schema/', $this->schemaModel)
                ->assertSee($this->schemaName)
                ->clickDeleteRecordButton($this->schemaName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/schema/', $this->schemaModel)
                ->assertDontSee($this->schemaName);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_schema_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->revokePermission('schema-delete');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->assertSourceMissing('button-delete');
        });
    }

    /** @test */
    public function an_admin_can_filter_schema_by_keyword()
    {
        $this->admin->grantPermission('schema-list');

        $this->createSchema();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->filterRecordsByText('#search-input', $this->schemaName)
                ->assertQueryStringHas('search', $this->schemaName)
                ->assertSee($this->schemaName)
                ->assertRecordsCount(1);
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_filter_schema_by_type()
    {
        $this->admin->grantPermission('schema-list');

        $schemaTypes = (new Schema)->schemaTypes();
        $firstValue = Arr::first($schemaTypes);
        $firstKey = key($schemaTypes);

        end($schemaTypes);

        $lastValue = Arr::last($schemaTypes);
        $lastKey = key($schemaTypes);

        $this->createSchema();

        $this->browse(function ($browser) use ($firstKey, $firstValue, $lastKey, $lastValue) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->filterRecordsBySelect('#type-input', $firstValue)
                ->assertQueryStringHas('type', $firstKey)
                ->assertRecordsCount(1)
                ->assertSee($this->schemaName)
                ->visit('/admin/schema')
                ->filterRecordsBySelect('#type-input', $lastValue)
                ->assertQueryStringHas('type', $lastKey)
                ->assertSee('No records found');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_filter_schema_by_target()
    {
        $this->admin->grantPermission('schema-list');

        $this->createSchema();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->filterRecordsBySelect('#target-input', $this->schemaTarget)
                ->assertQueryStringHas('target', User::class)
                ->assertRecordsCount(1);
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_filter_schema_by_start_date()
    {
        $this->admin->grantPermission('schema-list');

        $this->createSchema();

        $past = now()->subDays(7)->format('Y-m-d');
        $future = now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', urlencode($past))
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/schema', $this->schemaModel)
                ->assertSee($this->schemaName)
                ->visit('/admin/schema')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', urlencode($future))
                ->assertSee('No records found');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_filter_schema_by_end_date()
    {
        $this->admin->grantPermission('schema-list');

        $this->createSchema();

        $past = now()->subDays(7)->format('Y-m-d');
        $future = now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', urlencode($past))
                ->assertSee('No records found')
                ->visit('/admin/schema')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', urlencode($future))
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/schema', $this->schemaModel)
                ->assertSee($this->schemaName);
        });

        $this->deleteSchema();
    }

    /** @test */
    public function an_admin_can_clear_schema_filters()
    {
        $this->admin->grantPermission('schema-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema/?search=list&type=something&target=else&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('type')
                ->assertQueryStringHas('target')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/schema/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('type')
                ->assertQueryStringMissing('target')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_a_name_when_creating_a_schema()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema/create')
                ->clickLink('Article')
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->clickLink('Continue')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });
    }

    /** @test */
    public function it_requires_a_unique_name_when_creating_a_schema()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');

        $this->createSchema();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
               ->visit('/admin/schema/create')
               ->clickLink('Article')
               ->type('#name-input', $this->schemaName)
               ->typeSelect2('#target-input', $this->schemaTarget)
               ->clickLink('Continue')
                ->waitForText('The name has already been taken')
                ->assertSee('The name has already been taken');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function it_requires_a_target_when_creating_a_schema()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema/create')
                ->clickLink('Article')
                ->type('#name-input', $this->schemaName)
                ->clickLink('Continue')
                ->waitForText('The target field is required')
                ->assertSee('The target field is required');
        });
    }

    /** @test */
    public function it_requires_a_name_when_updating_a_schema()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-edit');

        $this->createSchema();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema/edit/' . $this->schemaModel->id)
                ->type('#name-input', '')
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });

        $this->deleteSchema();
    }

    /** @test */
    public function it_requires_a_unique_name_when_updating_a_schema()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-edit');

        $this->createSchema();
        $this->createSchemaModified();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/schema/edit/' . $this->schemaModel->id)
                ->type('#name-input', $this->schemaName)
                ->typeSelect2('#target-input', $this->schemaTarget)
                ->press('Save')
                ->waitForText('The name has already been taken')
                ->assertSee('The name has already been taken');
        });

        $this->deleteSchemaModified();
        $this->deleteSchema();
    }

    /** @test */
    public function it_requires_a_target_when_updating_a_schema()
    {
        $this->admin->grantPermission('schema-list');
        $this->admin->grantPermission('schema-edit');

        $this->createSchema();

        $this->browse(function ($browser) {
            $browser->resize(1250, 2500)->loginAs($this->admin, 'admin')
                ->visit('/admin/schema/edit/' . $this->schemaModel->id)
                ->type('#name-input', $this->schemaName)
                ->click('.select2-selection__clear')
                ->press('Save')
                ->waitForText('The target field is required')
                ->assertSee('The target field is required');
        });

        $this->deleteSchema();
    }

    /**
     * @return void
     */
    protected function createSchema()
    {
        $this->schemaModel = Schema::create([
            'name' => $this->schemaName,
            'target' => User::class,
            'type' => key((new Schema)->schemaTypes()),
        ]);
    }

    /**
     * @return void
     */
    protected function createSchemaModified()
    {
        $this->schemaModel = Schema::create([
            'name' => $this->schemaNameModified,
            'target' => User::class,
            'type' => key((new Schema)->schemaTypes()),
        ]);
    }

    /**
     * @return void
     */
    protected function deleteSchema()
    {
        Schema::whereName($this->schemaName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteSchemaModified()
    {
        Schema::whereName($this->schemaNameModified)->first()->delete();
    }
}
