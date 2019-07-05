<?php

namespace Varbox\Tests\Browser;

use Varbox\Models\Address;
use Varbox\Models\City;
use Varbox\Models\Country;
use Varbox\Models\State;
use Varbox\Models\User;

class AddressesTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var Address
     */
    protected $addressModel;

    /**
     * @var Country
     */
    protected $countryModel;

    /**
     * @var State
     */
    protected $stateModel;

    /**
     * @var City
     */
    protected $cityModel;

    /**
     * @var string
     */
    protected $addressAddress = 'Test Address';

    /**
     * @var string
     */
    protected $countryName = 'Test Country Name';
    protected $countryCode = 'TCC';

    /**
     * @var string
     */
    protected $stateName = 'Test State Name';
    protected $stateCode = 'TSC';

    /**
     * @var string
     */
    protected $cityName = 'Test City Name';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'email' => 'test-user@mail.com',
            'password' => bcrypt('test_password'),
        ]);
    }

    /** @test */
    public function an_admin_can_see_the_addresses_button_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/edit/' . $this->user->id)
                ->assertSee('View User\'s Addresses');
        });

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_can_see_the_addresses_button_if_it_has_permission()
    {
        $this->admin->grantPermission('users-edit');
        $this->admin->grantPermission('addresses-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/edit/' . $this->user->id)
                ->assertSee('View User\'s Addresses');
        });

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_cannot_see_the_addresses_button_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('users-edit');
        $this->admin->revokePermission('addresses-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/edit/' . $this->user->id)
                ->assertDontSee('View User\'s Addresses');
        });

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_can_access_user_addresses_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/edit/' . $this->user->id)
                ->clickLink('View User\'s Addresses')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses')
                ->assertSee('Addresses');
        });

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_can_access_user_addresses_if_it_has_permission()
    {
        $this->admin->grantPermission('users-edit');
        $this->admin->grantPermission('addresses-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/edit/' . $this->user->id)
                ->clickLink('View User\'s Addresses')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses')
                ->assertSee('Addresses');
        });

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses')
                ->assertSee('Addresses')
                ->assertSee('You are viewing the addresses for user: ' . $this->user->email);
        });

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('addresses-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses')
                ->assertSee('Addresses')
                ->assertSee('You are viewing the addresses for user: ' . $this->user->email);
        });

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('addresses-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->assertSee('Unauthorized')
                ->assertDontSee('Addresses');
        });

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->clickLink('Add New')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses/create')
                ->assertSee('Add Address')
                ->assertSee('You are viewing the addresses for user: ' . $this->user->email);
        });

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->grantPermission('addresses-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->clickLink('Add New')
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses/create')
                ->assertSee('Add Address')
                ->assertSee('You are viewing the addresses for user: ' . $this->user->email);
        });

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->revokePermission('addresses-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/' . $this->user->id . '/addresses')
                ->clickLink('Add New')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Address');
        });

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createAddress();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->clickEditButton($this->addressAddress)
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses/edit/' . $this->addressModel->id)
                ->assertSee('Edit Address')
                ->assertSee('You are viewing the addresses for user: ' . $this->user->email);
        });

        $this->deleteAddress();

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->grantPermission('addresses-edit');

        $this->createAddress();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->clickEditButton($this->addressAddress)
                ->assertPathIs('/admin/users/' . $this->user->id . '/addresses/edit/' . $this->addressModel->id)
                ->assertSee('Edit Address')
                ->assertSee('You are viewing the addresses for user: ' . $this->user->email);
        });

        $this->deleteAddress();

        $this->user->forceDelete();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('addresses-list');
        $this->admin->revokePermission('addresses-edit');

        $this->createAddress();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users/' . $this->user->id . '/addresses', $this->addressModel)
                ->clickEditButton($this->addressAddress)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Address');
        });

        $this->deleteAddress();

        $this->user->forceDelete();
    }

    /**
     * @return void
     */
    protected function createAddress()
    {
        $this->addressModel = $this->user->addresses()->create([
            'country_id' => optional($this->countryModel)->id ?: null,
            'state_id' => optional($this->stateModel)->id ?: null,
            'city_id' => optional($this->cityModel)->id ?: null,
            'address' => $this->addressAddress,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteAddress()
    {
        $this->addressModel->forceDelete();
    }
}
