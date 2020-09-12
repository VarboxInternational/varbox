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
                ->assertSee('401')
                ->assertDontSee('States');
        });
    }

    /** @test */
    public function an_admin_can_view_the_export_button_if_it_is_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_can_view_the_export_button_if_it_has_permission()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_export_button_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->revokePermission('states-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->assertSourceMissing('button-export');
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
                ->assertDontSee('Add New')
                ->visit('/admin/states/create')
                ->assertSee('401')
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
                ->clickEditRecordButton($this->stateName)
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
                ->clickEditRecordButton($this->stateName)
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
                ->assertSourceMissing('button-edit')
                ->visit('/admin/states/edit/' . $this->stateModel->id)
                ->assertSee('401')
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
                ->typeSelect2('#country_id-input', $this->countryName)
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
                ->typeSelect2('#country_id-input', $this->countryName)
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
                ->typeSelect2('#country_id-input', $this->countryName)
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

    /** @test */
    public function an_admin_can_update_a_state()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-edit');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/states', $this->stateModel)
                ->clickEditRecordButton($this->stateName)
                ->type('#name-input', $this->stateNameModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/states')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/states', $this->stateModel)
                ->assertSee($this->stateNameModified);
        });

        $this->deleteStateModified();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_update_a_state_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-edit');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/states', $this->stateModel)
                ->clickEditRecordButton($this->stateName)
                ->type('#name-input', $this->stateNameModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/states/edit/' . $this->stateModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#name-input', $this->stateNameModified);
        });

        $this->deleteStateModified();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_delete_a_state_if_it_has_permission()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-delete');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/states/', $this->stateModel)
                ->assertSee($this->stateName)
                ->assertSee($this->countryName)
                ->assertSee($this->stateCode)
                ->clickDeleteRecordButton($this->stateName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/states/', $this->stateModel)
                ->assertDontSee($this->stateName)
                ->assertDontSee($this->countryName)
                ->assertDontSee($this->stateCode);
        });

        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_cannot_delete_a_state_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->revokePermission('states-delete');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->assertSourceMissing('button-delete');
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_filter_states_by_keyword()
    {
        $this->admin->grantPermission('states-list');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->filterRecordsByText('#search-input', $this->stateName)
                ->assertQueryStringHas('search', $this->stateName)
                ->assertSee($this->stateName)
                ->assertRecordsCount(1)
                ->visit('/admin/states')
                ->filterRecordsByText('#search-input', $this->stateNameModified)
                ->assertQueryStringHas('search', $this->stateNameModified)
                ->assertSee('No records found');
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_filter_states_by_country()
    {
        $this->admin->grantPermission('states-list');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->filterRecordsBySelect('#country-input', $this->countryName)
                ->assertQueryStringHas('country', $this->countryModel->id)
                ->assertSee($this->stateName)
                ->assertSee($this->countryName)
                ->assertRecordsCount(1);
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_filter_states_by_start_date()
    {
        $this->admin->grantPermission('states-list');

        $this->createCountry();
        $this->createState();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/states', $this->stateModel)
                ->assertSee($this->stateName)
                ->visit('/admin/states')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_filter_states_by_end_date()
    {
        $this->admin->grantPermission('states-list');

        $this->createCountry();
        $this->createState();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/states')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', $future)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/states', $this->stateModel)
                ->assertSee($this->stateName);
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function an_admin_can_clear_state_filters()
    {
        $this->admin->grantPermission('states-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states/?search=something&country=1000&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('country')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/states/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('country')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_a_name_when_creating_a_state()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-add');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->clickLink('Add New')
                ->typeSelect2('#country_id-input', $this->countryName)
                ->type('#code-input', $this->stateCode)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });

        $this->deleteCountry();
    }

    /** @test */
    public function it_requires_a_code_when_creating_a_state()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-add');

        $this->createCountry();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->clickLink('Add New')
                ->typeSelect2('#country_id-input', $this->countryName)
                ->type('#name-input', $this->stateName)
                ->press('Save')
                ->waitForText('The code field is required')
                ->assertSee('The code field is required');
        });

        $this->deleteCountry();
    }

    /** @test */
    public function it_requires_a_country_when_creating_a_state()
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
                ->press('Save')
                ->waitForText('The country field is required')
                ->assertSee('The country field is required');
        });

        $this->deleteCountry();
    }

    /** @test */
    public function it_requires_a_name_when_updating_a_state()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-edit');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->clickEditRecordButton($this->stateName)
                ->type('#name-input', '')
                ->type('#code-input', $this->stateCode)
                ->typeSelect2('#copuntry_id-input', $this->countryName)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function it_requires_a_code_when_updating_a_state()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-edit');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->clickEditRecordButton($this->stateName)
                ->type('#name-input', $this->stateName)
                ->type('#code-input', '')
                ->typeSelect2('#copuntry_id-input', $this->countryName)
                ->press('Save')
                ->waitForText('The code field is required')
                ->assertSee('The code field is required');
        });

        $this->deleteState();
        $this->deleteCountry();
    }

    /** @test */
    public function it_requires_a_country_when_updating_a_state()
    {
        $this->admin->grantPermission('states-list');
        $this->admin->grantPermission('states-edit');

        $this->createCountry();
        $this->createState();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/states')
                ->clickEditRecordButton($this->stateName)
                ->type('#name-input', $this->stateName)
                ->type('#code-input', $this->stateCode)
                ->click('.select2-selection__clear')
                ->press('Save')
                ->waitForText('The country field is required')
                ->assertSee('The country field is required');
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
    protected function deleteStateModified()
    {
        State::whereName($this->stateNameModified)->first()->delete();
    }
}
