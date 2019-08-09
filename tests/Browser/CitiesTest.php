<?php

namespace Varbox\Tests\Browser;

use Varbox\Models\City;
use Varbox\Models\Country;
use Varbox\Models\State;

class CitiesTest extends TestCase
{
    /**
     * @var City
     */
    protected $cityModel;

    /**
     * @var State
     */
    protected $stateModel;

    /**
     * @var Country
     */
    protected $countryModel;

    /**
     * @var string
     */
    protected $cityName = 'Test City Name';
    protected $cityNameModified = 'Test City Name Modified';

    /**
     * @var string
     */
    protected $stateName = 'Test State Name';
    protected $stateCode = 'TSC';

    /**
     * @var string
     */
    protected $countryName = 'Test Country Name';
    protected $countryCode = 'TCC';

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->assertPathIs('/admin/cities')
                ->assertSee('Cities');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('cities-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->assertPathIs('/admin/cities')
                ->assertSee('Cities');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('cities-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->assertSee('Unauthorized')
                ->assertDontSee('Cities');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->clickLink('Add New')
                ->assertPathIs('/admin/cities/create')
                ->assertSee('Add City');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->clickLink('Add New')
                ->assertPathIs('/admin/cities/create')
                ->assertSee('Add City');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->revokePermission('cities-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->assertDontSee('Add New')
                ->visit('/admin/cities/create')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add City');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/cities', $this->cityModel)
                ->clickEditRecordButton($this->cityName)
                ->assertPathIs('/admin/cities/edit/' . $this->cityModel->id)
                ->assertSee('Edit City');
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-edit');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/cities', $this->cityModel)
                ->clickEditRecordButton($this->cityName)
                ->assertPathIs('/admin/cities/edit/' . $this->cityModel->id)
                ->assertSee('Edit City');
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->revokePermission('cities-edit');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/cities', $this->cityModel)
                ->assertSourceMissing('button-edit')
                ->visit('/admin/cities/edit/' . $this->cityModel->id)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit City');
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_create_a_city()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-add');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->clickLink('Add New')
                ->type('#name-input', $this->cityName)
                ->typeSelect2('#country_id-input', $this->countryName)
                ->typeSelect2('#state_id-input', $this->stateName)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/cities')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/cities/', new City)
                ->assertSee($this->cityName)
                ->assertSee($this->stateName)
                ->assertSee($this->countryName);
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_create_a_city_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-add');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->clickLink('Add New')
                ->type('#name-input', $this->cityName)
                ->typeSelect2('#country_id-input', $this->countryName)
                ->typeSelect2('#state_id-input', $this->stateName)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/cities/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_create_a_city_and_continue_editing_it()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-add');
        $this->admin->grantPermission('cities-edit');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->clickLink('Add New')
                ->type('#name-input', $this->cityName)
                ->typeSelect2('#country_id-input', $this->countryName)
                ->typeSelect2('#state_id-input', $this->stateName)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/cities/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#name-input', $this->cityName)
                ->assertSee($this->countryName)
                ->assertSee($this->stateName);
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_update_a_city()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-edit');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/cities', $this->cityModel)
                ->clickEditRecordButton($this->cityName)
                ->type('#name-input', $this->cityNameModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/cities')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/cities', $this->cityModel)
                ->assertSee($this->cityNameModified);
        });

        $this->deleteCityModified();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_update_a_city_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-edit');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/cities', $this->cityModel)
                ->clickEditRecordButton($this->cityName)
                ->type('#name-input', $this->cityNameModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/cities/edit/' . $this->cityModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#name-input', $this->cityNameModified);
        });

        $this->deleteCityModified();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_delete_a_city_if_it_has_permission()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-delete');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/cities/', $this->cityModel)
                ->assertSee($this->cityName)
                ->assertSee($this->countryName)
                ->assertSee($this->stateName)
                ->clickDeleteRecordButton($this->cityName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/cities/', $this->cityModel)
                ->assertDontSee($this->cityName)
                ->assertDontSee($this->countryName)
                ->assertDontSee($this->stateName);
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_cannot_delete_a_city_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->revokePermission('cities-delete');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->assertSourceMissing('button-delete');
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_filter_cities_by_keyword()
    {
        $this->admin->grantPermission('cities-list');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->filterRecordsByText('#search-input', $this->cityName)
                ->assertQueryStringHas('search', $this->cityName)
                ->assertSee($this->cityName)
                ->assertRecordsCount(1)
                ->visit('/admin/cities')
                ->filterRecordsByText('#search-input', $this->cityNameModified)
                ->assertQueryStringHas('search', $this->cityNameModified)
                ->assertSee('No records found');
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_filter_cities_by_country()
    {
        $this->admin->grantPermission('cities-list');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->filterRecordsBySelect('#country-input', $this->countryName)
                ->assertQueryStringHas('country', $this->countryModel->id)
                ->assertSee($this->cityName)
                ->assertSee($this->countryName)
                ->assertRecordsCount(1);
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_filter_cities_by_state()
    {
        $this->admin->grantPermission('cities-list');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->filterRecordsBySelect('#country-input', $this->countryName)
                ->filterRecordsBySelect('#state-input', $this->stateName, true)
                ->assertQueryStringHas('state', $this->stateModel->id)
                ->assertSee($this->cityName)
                ->assertSee($this->stateName)
                ->assertRecordsCount(1);
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_only_filter_by_states_belonging_to_the_selected_country()
    {
        $this->admin->grantPermission('cities-list');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->click('.filter-records-container')
                ->waitForText('Filter')
                ->click('#state-input + .select2')
                ->assertSee('No results found');
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_filter_cities_by_start_date()
    {
        $this->admin->grantPermission('cities-list');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/cities', $this->cityModel)
                ->assertSee($this->cityName)
                ->visit('/admin/cities')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_filter_cities_by_end_date()
    {
        $this->admin->grantPermission('cities-list');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/cities')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', $future)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/cities', $this->cityModel)
                ->assertSee($this->cityName);
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_clear_city_filters()
    {
        $this->admin->grantPermission('cities-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities/?search=something&country=1000&state=100&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('country')
                ->assertQueryStringHas('state')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/cities/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('country')
                ->assertQueryStringMissing('state')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_a_name_when_creating_a_city()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-add');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->clickLink('Add New')
                ->typeSelect2('#country_id-input', $this->countryName)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });

        $this->deleteCountry();
    }

    /** @test */
    public function it_requires_a_country_when_creating_a_city()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-add');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->clickLink('Add New')
                ->type('#name-input', $this->cityName)
                ->press('Save')
                ->waitForText('The country field is required')
                ->assertSee('The country field is required');
        });

        $this->deleteCountry();
    }

    /** @test */
    public function it_requires_a_name_when_updating_a_city()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-edit');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->clickEditRecordButton($this->cityName)
                ->type('#name-input', '')
                ->typeSelect2('#copuntry_id-input', $this->countryName)
                ->typeSelect2('#state_id-input', $this->stateName)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function it_requires_a_country_when_updating_a_city()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-edit');

        $this->createCountry();
        $this->createState();
        $this->createCity();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/cities')
                ->clickEditRecordButton($this->cityName)
                ->type('#name-input', $this->stateName)
                ->click('.select2-selection__clear')
                ->press('Save')
                ->waitForText('The country field is required')
                ->assertSee('The country field is required');
        });

        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }

    /**
     * @return void
     */
    protected function createCountry()
    {
        $this->countryModel = Country::create([
            'name' => $this->countryName,
            'code' => $this->countryCode,
        ]);
    }

    /**
     * @return void
     */
    protected function createState()
    {
        $this->stateModel = State::create([
            'country_id' => $this->countryModel->id,
            'name' => $this->stateName,
            'code' => $this->stateCode,
        ]);
    }

    /**
     * @return void
     */
    protected function createCity()
    {
        $this->cityModel = City::create([
            'country_id' => $this->countryModel->id,
            'state_id' => $this->stateModel->id,
            'name' => $this->cityName,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteCountry()
    {
        Country::whereName($this->countryName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteState()
    {
        State::whereName($this->stateName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteCity()
    {
        City::whereName($this->cityName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteCityModified()
    {
        City::whereName($this->cityNameModified)->first()->delete();
    }
}
