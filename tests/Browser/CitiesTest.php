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
                ->clickLink('Add New')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add City');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createAll();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/cities', $this->cityModel)
                ->clickEditButton($this->cityName)
                ->assertPathIs('/admin/cities/edit/' . $this->cityModel->id)
                ->assertSee('Edit City');
        });

        $this->deleteAll();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->grantPermission('cities-edit');

        $this->createAll();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/cities', $this->cityModel)
                ->clickEditButton($this->cityName)
                ->assertPathIs('/admin/cities/edit/' . $this->cityModel->id)
                ->assertSee('Edit City');
        });

        $this->deleteAll();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('cities-list');
        $this->admin->revokePermission('cities-edit');

        $this->createAll();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/cities', $this->cityModel)
                ->clickEditButton($this->cityName)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit City');
        });

        $this->deleteAll();
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
                ->select2('#country_id-input', $this->countryName)
                ->select2('#state_id-input', $this->stateName)
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
                ->select2('#country_id-input', $this->countryName)
                ->select2('#state_id-input', $this->stateName)
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
                ->select2('#country_id-input', $this->countryName)
                ->select2('#state_id-input', $this->stateName)
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
                ->clickEditButton($this->cityName)
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
                ->clickEditButton($this->cityName)
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
                ->deleteRecord($this->cityName)
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
                ->deleteAnyRecord()
                ->assertDontSee('The record was successfully deleted!')
                ->assertSee('Unauthorized');
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
    protected function createAll()
    {
        $this->createCountry();
        $this->createState();
        $this->createCity();
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

    /**
     * @return void
     */
    protected function deleteAll()
    {
        $this->deleteCity();
        $this->deleteState();
        $this->deleteCountry();
    }
}
