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
        State::whereName($this->cityNameModified)->first()->delete();
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
