<?php

namespace Varbox\Tests\Browser;

use Carbon\Carbon;
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
