<?php

namespace Varbox\Tests\Browser;

use Carbon\Carbon;
use Varbox\Models\Role;

class RolesTest extends TestCase
{
    /**
     * @var Role
     */
    protected $roleModel;

    /**
     * @var string
     */
    protected $roleName = 'test-role-name';
    protected $roleNameModified = 'test-role-name-modified';

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->assertPathIs('/admin/roles')
                ->assertSee('Roles');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('roles-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->assertPathIs('/admin/roles')
                ->assertSee('Roles');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('roles-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->assertSee('Unauthorized')
                ->assertDontSee('Roles');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->clickLink('Add New')
                ->assertPathIs('/admin/roles/create')
                ->assertSee('Add Role');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->grantPermission('roles-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->clickLink('Add New')
                ->assertPathIs('/admin/roles/create')
                ->assertSee('Add Role');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->revokePermission('roles-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->clickLink('Add New')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Role');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createRole();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/roles', $this->roleModel)
                ->clickEditButton($this->roleName)
                ->assertPathIs('/admin/roles/edit/' . $this->roleModel->id)
                ->assertSee('Edit Role');
        });

        $this->deleteRole();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->grantPermission('roles-edit');

        $this->createRole();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/roles', $this->roleModel)
                ->clickEditButton($this->roleName)
                ->assertPathIs('/admin/roles/edit/' . $this->roleModel->id)
                ->assertSee('Edit Role');
        });

        $this->deleteRole();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->revokePermission('roles-edit');

        $this->createRole();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/roles', $this->roleModel)
                ->clickEditButton($this->roleName)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Role');
        });

        $this->deleteRole();
    }

    /** @test */
    public function an_admin_can_create_a_role()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->grantPermission('roles-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->clickLink('Add New')
                ->type('#name-input', $this->roleName)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/roles')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/roles/', new Role)
                ->assertSee($this->roleName);
        });

        $this->deleteRole();
    }

    /** @test */
    public function an_admin_can_create_a_role_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->grantPermission('roles-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->clickLink('Add New')
                ->type('#name-input', $this->roleName)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/roles/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deleteRole();
    }

    /** @test */
    public function an_admin_can_create_a_role_and_continue_editing_it()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->grantPermission('roles-add');
        $this->admin->grantPermission('roles-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->clickLink('Add New')
                ->type('#name-input', $this->roleName)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/roles/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#name-input', $this->roleName);
        });

        $this->deleteRole();
    }

    /** @test */
    public function an_admin_can_update_a_role()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->grantPermission('roles-edit');

        $this->createRole();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/roles', $this->roleModel)
                ->clickEditButton($this->roleName)
                ->type('#name-input', $this->roleNameModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/roles')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/roles', $this->roleModel)
                ->assertSee($this->roleNameModified);
        });

        $this->deleteRoleModified();
    }

    /** @test */
    public function an_admin_can_update_a_role_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->grantPermission('roles-edit');

        $this->createRole();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/roles', $this->roleModel)
                ->clickEditButton($this->roleName)
                ->type('#name-input', $this->roleNameModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/roles/edit/' . $this->roleModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#name-input', $this->roleNameModified);
        });

        $this->deleteRoleModified();
    }

    /** @test */
    public function an_admin_can_delete_a_role_if_it_has_permission()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->grantPermission('roles-delete');

        $this->createRole();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/roles/', $this->roleModel)
                ->assertSee($this->roleName)
                ->deleteRecord($this->roleName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/roles/', $this->roleModel)
                ->assertDontSee($this->roleName);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_role_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->revokePermission('roles-delete');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->deleteAnyRecord()
                ->assertDontSee('The record was successfully deleted!')
                ->assertSee('Unauthorized');
        });
    }

    /** @test */
    public function an_admin_can_filter_roles_by_keyword()
    {
        $this->admin->grantPermission('roles-list');

        $this->createRole();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->filterRecordsByText('#search-input', $this->roleName)
                ->assertQueryStringHas('search', urlencode($this->roleName))
                ->assertSee($this->roleName)
                ->assertRecordsCount(1);
        });

        $this->deleteRole();
    }

    /** @test */
    public function an_admin_can_filter_roles_by_guard()
    {
        $this->admin->grantPermission('roles-list');

        $this->createRole();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->filterRecordsBySelect('#guard-input', 'web')
                ->assertQueryStringHas('guard', urlencode('web'))
                ->assertRecordsCount(1)
                ->assertSee($this->roleName)
                ->visit('/admin/roles')
                ->filterRecordsBySelect('#guard-input', 'api')
                ->assertQueryStringHas('guard', urlencode('api'))
                ->assertSee('No records found');
        });

        $this->deleteRole();
    }

    /** @test */
    public function an_admin_can_filter_roles_by_start_date()
    {
        $this->admin->grantPermission('roles-list');

        $this->createRole();

        $past = Carbon::now()->subDays(7)->format('Y-m-d');
        $future = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', urlencode($past))
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/roles', $this->roleModel)
                ->assertSee($this->roleName)
                ->visit('/admin/roles')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', urlencode($future))
                ->assertSee('No records found');
        });

        $this->deleteRole();
    }

    /** @test */
    public function an_admin_can_filter_roles_by_end_date()
    {
        $this->admin->grantPermission('roles-list');

        $this->createRole();

        $past = Carbon::now()->subDays(7)->format('Y-m-d');
        $future = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', urlencode($past))
                ->assertSee('No records found')
                ->visit('/admin/roles')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', urlencode($future))
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/roles', $this->roleModel)
                ->assertSee($this->roleName);
        });

        $this->deleteRole();
    }

    /** @test */
    public function an_admin_can_clear_role_filters()
    {
        $this->admin->grantPermission('roles-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles/?search=list&guard=web&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('guard')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/roles/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('guard')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_a_name_when_creating_a_role()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->grantPermission('roles-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->clickLink('Add New')
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });
    }

    /** @test */
    public function it_requires_a_name_when_updating_a_role()
    {
        $this->admin->grantPermission('roles-list');
        $this->admin->grantPermission('roles-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/roles')
                ->click('.button-edit')
                ->type('#name-input', '')
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });
    }

    /**
     * @return void
     */
    protected function createRole()
    {
        $this->roleModel = Role::create([
            'guard' => 'web',
            'name' => $this->roleName,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteRole()
    {
        Role::whereName($this->roleName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteRoleModified()
    {
        Role::whereName($this->roleNameModified)->first()->delete();
    }
}
