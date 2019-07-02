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

        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/states', $this->stateModel)
                ->clickEditButton($this->stateName)
                ->assertPathIs('/admin/states/edit/' . $this->stateModel->id)
                ->assertSee('Edit State');
        });

        $this->deleteState();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-edit');

        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/states', $this->stateModel)
                ->clickEditButton($this->stateName)
                ->assertPathIs('/admin/states/edit/' . $this->stateModel->id)
                ->assertSee('Edit State');
        });

        $this->deleteState();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->revokePermission('states-edit');

        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/states', $this->stateModel)
                ->clickEditButton($this->stateName)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit State');
        });

        $this->deleteState();
    }

    /**
     * @return void
     */
    protected function createState()
    {
        $this->countryModel = Country::create([
            'name' => $this->countryName,
            'code' => $this->countryCode,
        ]);

        $this->stateModel = $this->countryModel->states()->create([
            'name' => $this->stateName,
            'code' => $this->stateCode,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteState()
    {
        State::whereName($this->stateName)->first()->delete();
        Country::whereName($this->countryName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteStateModified()
    {
        State::whereName($this->stateNameModified)->first()->delete();
        Country::whereName($this->countryNameModified)->first()->delete();
    }
}
