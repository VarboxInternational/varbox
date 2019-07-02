<?php

namespace Varbox\Tests\Browser;

use Varbox\Models\Country;

class CountriesTest extends TestCase
{
    /**
     * @var Country
     */
    protected $countryModel;

    /**
     * @var string
     */
    protected $countryName = 'Test Country Name';
    protected $countryCode = 'TCC';

    /**
     * @var string
     */
    protected $countryNameModified = 'Test Country Name Modified';

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->assertPathIs('/admin/countries')
                ->assertSee('Countries');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('countries-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->assertPathIs('/admin/countries')
                ->assertSee('Countries');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('countries-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->assertSee('Unauthorized')
                ->assertDontSee('Countries');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->clickLink('Add New')
                ->assertPathIs('/admin/countries/create')
                ->assertSee('Add Country');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->grantPermission('countries-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->clickLink('Add New')
                ->assertPathIs('/admin/countries/create')
                ->assertSee('Add Country');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->revokePermission('countries-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->clickLink('Add New')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Country');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/countries', $this->countryModel)
                ->clickEditButton($this->countryName)
                ->assertPathIs('/admin/countries/edit/' . $this->countryModel->id)
                ->assertSee('Edit Country');
        });

        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->grantPermission('countries-edit');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/countries', $this->countryModel)
                ->clickEditButton($this->countryName)
                ->assertPathIs('/admin/countries/edit/' . $this->countryModel->id)
                ->assertSee('Edit Country');
        });

        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->revokePermission('countries-edit');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/countries', $this->countryModel)
                ->clickEditButton($this->countryName)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Country');
        });

        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_create_a_country()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->grantPermission('countries-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->clickLink('Add New')
                ->type('#name-input', $this->countryName)
                ->type('#code-input', $this->countryCode)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/countries')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/countries/', new Country)
                ->assertSee($this->countryName)
                ->assertSee($this->countryCode);
        });

        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_create_a_country_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->grantPermission('countries-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->clickLink('Add New')
                ->type('#name-input', $this->countryName)
                ->type('#code-input', $this->countryCode)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/countries/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_create_a_country_and_continue_editing_it()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->grantPermission('countries-add');
        $this->admin->grantPermission('countries-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->clickLink('Add New')
                ->type('#name-input', $this->countryName)
                ->type('#code-input', $this->countryCode)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/countries/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#name-input', $this->countryName)
                ->assertInputValue('#code-input', $this->countryCode);
        });

        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_update_a_country()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->grantPermission('countries-edit');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/countries', $this->countryModel)
                ->clickEditButton($this->countryName)
                ->type('#name-input', $this->countryNameModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/countries')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/countries', $this->countryModel)
                ->assertSee($this->countryNameModified);
        });

        $this->deleteCountryModified();
    }

    /** @test */
    public function an_admin_can_update_a_country_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->grantPermission('countries-edit');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/countries', $this->countryModel)
                ->clickEditButton($this->countryName)
                ->type('#name-input', $this->countryNameModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/countries/edit/' . $this->countryModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#name-input', $this->countryNameModified);
        });

        $this->deleteCountryModified();
    }

    /** @test */
    public function an_admin_can_delete_a_country_if_it_has_permission()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->grantPermission('countries-delete');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/countries/', $this->countryModel)
                ->assertSee($this->countryName)
                ->assertSee($this->countryCode)
                ->deleteRecord($this->countryName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/countries/', $this->countryModel)
                ->assertDontSee($this->countryName)
                ->assertDontSee($this->countryCode);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_country_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->revokePermission('countries-delete');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->deleteAnyRecord()
                ->assertDontSee('The record was successfully deleted!')
                ->assertSee('Unauthorized');
        });
    }

    /** @test */
    public function an_admin_can_filter_countries_by_keyword()
    {
        $this->admin->grantPermission('roles-list');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->filterRecordsByText('#search-input', $this->countryName)
                ->assertQueryStringHas('search', $this->countryName)
                ->assertSee($this->countryName)
                ->assertRecordsCount(1);
        });

        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_filter_countries_by_start_date()
    {
        $this->admin->grantPermission('countries-list');

        $this->createCountry();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/countries', $this->countryModel)
                ->assertSee($this->countryName)
                ->visit('/admin/countries')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_filter_countries_by_end_date()
    {
        $this->admin->grantPermission('countries-list');

        $this->createCountry();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/countries')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', $future)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/countries', $this->countryModel)
                ->assertSee($this->countryName);
        });

        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_clear_country_filters()
    {
        $this->admin->grantPermission('countries-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries/?search=a&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/countries/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_a_name_when_creating_a_country()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->grantPermission('countries-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->clickLink('Add New')
                ->type('#code-input', $this->countryCode)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });
    }

    /** @test */
    public function it_requires_a_code_when_creating_a_country()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->grantPermission('countries-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->clickLink('Add New')
                ->type('#name-input', $this->countryName)
                ->press('Save')
                ->waitForText('The code field is required')
                ->assertSee('The code field is required');
        });
    }

    /** @test */
    public function it_requires_a_name_when_updating_a_country()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->grantPermission('countries-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->click('.button-edit')
                ->type('#name-input', '')
                ->type('#code-input', $this->countryCode)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });
    }

    /** @test */
    public function it_requires_a_code_when_updating_a_country()
    {
        $this->admin->grantPermission('countries-list');
        $this->admin->grantPermission('countries-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/countries')
                ->click('.button-edit')
                ->type('#code-input', '')
                ->type('#name-input', $this->countryName)
                ->press('Save')
                ->waitForText('The code field is required')
                ->assertSee('The code field is required');
        });
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
    protected function deleteCountry()
    {
        Country::whereName($this->countryName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteCountryModified()
    {
        Country::whereName($this->countryNameModified)->first()->delete();
    }
}
