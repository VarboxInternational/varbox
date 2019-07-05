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

        $this->user->delete();
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

        $this->user->delete();
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

        $this->user->delete();
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

        $this->user->delete();
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

        $this->user->delete();
    }
}
