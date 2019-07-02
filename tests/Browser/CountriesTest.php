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
