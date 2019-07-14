<?php

namespace Varbox\Tests\Browser;

class BackupsTest extends TestCase
{
    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->assertPathIs('/admin/backups')
                ->assertSee('Backups');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('backups-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->assertPathIs('/admin/backups')
                ->assertSee('Backups');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('backups-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/backups')
                ->assertSee('Unauthorized')
                ->assertDontSee('Backups');
        });
    }
}
