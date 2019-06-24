<?php

namespace Varbox\Tests\Browser;

class AuthTest extends TestCase
{
    /** @test */
    public function an_admin_user_can_sign_in_to_the_admin_panel()
    {
        $this->browse(function ($browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/admin/login')
                ->type('email', $this->admin->email)
                ->type('password', 'admin')
                ->press('Sign In')
                ->assertPathIs('/admin');
        });
    }

    /** @test */
    public function non_admin_users_cannot_sign_in_to_the_admin_panel()
    {
        $this->admin->removeRoles('Admin');

        $this->browse(function ($browser) {
            $browser->driver->manage()->deleteAllCookies();
            $browser->visit('/admin/login')
                ->type('email', $this->admin->email)
                ->type('password', 'admin')
                ->press('Sign In')
                ->assertPathIs('/admin/login')
                ->assertSee('These credentials do not match our records');
        });
    }
}
