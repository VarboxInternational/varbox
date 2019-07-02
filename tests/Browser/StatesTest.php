<?php

namespace Varbox\Tests\Browser;

use Varbox\Models\Country;
use Varbox\Models\State;

class StatesTest extends TestCase
{
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
    protected $stateName = 'Test State Name';
    protected $stateCode = 'TSC';

    /**
     * @var string
     */
    protected $countryName = 'Test Country Name';
    protected $countryCode = 'TCC';

    /**
     * @var string
     */
    protected $stateNameModified = 'Test State Name Modified';

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
                ->visit('/admin/states')
                ->assertPathIs('/admin/states')
                ->assertSee('States');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('states-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->assertPathIs('/admin/states')
                ->assertSee('States');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('states-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->assertSee('Unauthorized')
                ->assertDontSee('States');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->clickLink('Add New')
                ->assertPathIs('/admin/states/create')
                ->assertSee('Add State');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->clickLink('Add New')
                ->assertPathIs('/admin/states/create')
                ->assertSee('Add State');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->revokePermission('states-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->clickLink('Add New')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add State');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/states', $this->stateModel)
                ->clickEditButton($this->stateName)
                ->assertPathIs('/admin/states/edit/' . $this->stateModel->id)
                ->assertSee('Edit State');
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-edit');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/states', $this->stateModel)
                ->clickEditButton($this->stateName)
                ->assertPathIs('/admin/states/edit/' . $this->stateModel->id)
                ->assertSee('Edit State');
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->revokePermission('states-edit');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/states', $this->stateModel)
                ->clickEditButton($this->stateName)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit State');
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_create_a_state()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-add');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->clickLink('Add New')
                ->type('#name-input', $this->stateName)
                ->type('#code-input', $this->stateCode)
                ->select2('#country_id-input', $this->countryName)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/states')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/states/', new State)
                ->assertSee($this->stateName)
                ->assertSee($this->stateCode)
                ->assertSee($this->countryName);
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_create_a_state_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-add');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->clickLink('Add New')
                ->type('#name-input', $this->stateName)
                ->type('#code-input', $this->stateCode)
                ->select2('#country_id-input', $this->countryName)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/states/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_create_a_state_and_continue_editing_it()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-add');
        $this->admin->grantPermission('states-edit');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->clickLink('Add New')
                ->type('#name-input', $this->stateName)
                ->type('#code-input', $this->stateCode)
                ->select2('#country_id-input', $this->countryName)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/states/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#name-input', $this->stateName)
                ->assertInputValue('#code-input', $this->stateCode)
                ->assertSee($this->countryName);
        });

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
        $this->stateModel = $this->countryModel->states()->create([
            'name' => $this->stateName,
            'code' => $this->stateCode,
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
    protected function deleteCountryModified()
    {
        Country::whereName($this->countryNameModified)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteStateModified()
    {
        State::whereName($this->stateNameModified)->first()->delete();
    }
}
