<?php

namespace Varbox\Tests\Browser;

class ErrorsTest extends TestCase
{
    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->assertPathIs('/admin/errors')
                ->assertSee('Errors');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('errors-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->assertPathIs('/admin/errors')
                ->assertSee('Errors');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('errors-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->assertSee('Unauthorized')
                ->assertDontSee('Errors');
        });
    }
}
