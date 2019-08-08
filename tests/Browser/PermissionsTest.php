<?php

namespace Varbox\Tests\Browser;

use Carbon\Carbon;
use Varbox\Models\Permission;

class PermissionsTest extends TestCase
{
    /**
     * @var Permission
     */
    protected $permissionModel;

    /**
     * @var string
     */
    protected $permissionName = 'test-permission-name';
    protected $permissionLabel = 'Test permission label';
    protected $permissionGroup = 'Test permission group';

    /**
     * @var string
     */
    protected $permissionNameModified = 'test-permission-name-modified';

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->assertPathIs('/admin/permissions')
                ->assertSee('Permissions');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('permissions-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->assertPathIs('/admin/permissions')
                ->assertSee('Permissions');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('permissions-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->assertSee('Unauthorized')
                ->assertDontSee('Permissions');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->clickLink('Add New')
                ->assertPathIs('/admin/permissions/create')
                ->assertSee('Add Permission');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->grantPermission('permissions-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->clickLink('Add New')
                ->assertPathIs('/admin/permissions/create')
                ->assertSee('Add Permission');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->revokePermission('permissions-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->clickLink('Add New')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Permission');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createPermission();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/permissions', $this->permissionModel)
                ->clickEditRecordButton($this->permissionName)
                ->assertPathIs('/admin/permissions/edit/' . $this->permissionModel->id)
                ->assertSee('Edit Permission');
        });

        $this->deletePermission();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->grantPermission('permissions-edit');

        $this->createPermission();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/permissions', $this->permissionModel)
                ->clickEditRecordButton($this->permissionName)
                ->assertPathIs('/admin/permissions/edit/' . $this->permissionModel->id)
                ->assertSee('Edit Permission');
        });

        $this->deletePermission();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->revokePermission('permissions-edit');

        $this->createPermission();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/permissions', $this->permissionModel)
                ->clickEditRecordButton($this->permissionName)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Permission');
        });

        $this->deletePermission();
    }

    /** @test */
    public function an_admin_can_create_a_permission()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->grantPermission('permissions-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->clickLink('Add New')
                ->type('#name-input', $this->permissionName)
                ->type('#group-input', $this->permissionGroup)
                ->type('#label-input', $this->permissionLabel)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/permissions')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/permissions/', new Permission)
                ->assertSee($this->permissionName);
        });

        $this->deletePermission();
    }

    /** @test */
    public function an_admin_can_create_a_permission_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->grantPermission('permissions-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->clickLink('Add New')
                ->type('#name-input', $this->permissionName)
                ->type('#group-input', $this->permissionGroup)
                ->type('#label-input', $this->permissionLabel)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/permissions/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deletePermission();
    }

    /** @test */
    public function an_admin_can_create_a_permission_and_continue_editing_it()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->grantPermission('permissions-add');
        $this->admin->grantPermission('permissions-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->clickLink('Add New')
                ->type('#name-input', $this->permissionName)
                ->type('#group-input', $this->permissionGroup)
                ->type('#label-input', $this->permissionLabel)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/permissions/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#name-input', $this->permissionName)
                ->assertInputValue('#group-input', $this->permissionGroup)
                ->assertInputValue('#label-input', $this->permissionLabel);
        });

        $this->deletePermission();
    }

    /** @test */
    public function an_admin_can_update_a_permission()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->grantPermission('permissions-edit');

        $this->createPermission();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/permissions', $this->permissionModel)
                ->clickEditRecordButton($this->permissionName)
                ->type('#name-input', $this->permissionNameModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/permissions')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/permissions', $this->permissionModel)
                ->assertSee($this->permissionNameModified);
        });

        $this->deletePermissionModified();
    }

    /** @test */
    public function an_admin_can_update_a_permission_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->grantPermission('permissions-edit');

        $this->createPermission();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/permissions', $this->permissionModel)
                ->clickEditRecordButton($this->permissionName)
                ->type('#name-input', $this->permissionNameModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/permissions/edit/' . $this->permissionModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#name-input', $this->permissionNameModified);
        });

        $this->deletePermissionModified();
    }

    /** @test */
    public function an_admin_can_delete_a_permission_if_it_has_permission()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->grantPermission('permissions-delete');

        $this->createPermission();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/permissions/', $this->permissionModel)
                ->assertSee($this->permissionName)
                ->clickDeleteRecordButton($this->permissionName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/permissions/', $this->permissionModel)
                ->assertDontSee($this->permissionName);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_permission_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->revokePermission('permissions-delete');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->clickDeleteAnyRecordButton()
                ->assertDontSee('The record was successfully deleted!')
                ->assertSee('Unauthorized');
        });
    }

    /** @test */
    public function an_admin_can_filter_permissions_by_keyword()
    {
        $this->admin->grantPermission('permissions-list');

        $this->createPermission();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->filterRecordsByText('#search-input', $this->permissionName)
                ->assertQueryStringHas('search', $this->permissionName)
                ->assertSee($this->permissionName)
                ->assertRecordsCount(1);
        });

        $this->deletePermission();
    }

    /** @test */
    public function an_admin_can_filter_permissions_by_guard()
    {
        $this->admin->grantPermission('permissions-list');

        $this->createPermission();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->filterRecordsBySelect('#guard-input', 'Web')
                ->assertQueryStringHas('guard', 'web')
                ->assertRecordsCount(1)
                ->assertSee($this->permissionName)
                ->visit('/admin/permissions')
                ->filterRecordsBySelect('#guard-input', 'Api')
                ->assertQueryStringHas('guard', 'api')
                ->assertSee('No records found');
        });

        $this->deletePermission();
    }

    /** @test */
    public function an_admin_can_filter_permissions_by_start_date()
    {
        $this->admin->grantPermission('permissions-list');

        $this->createPermission();

        $past = Carbon::now()->subDays(7)->format('Y-m-d');
        $future = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/permissions', $this->permissionModel)
                ->assertSee($this->permissionName)
                ->visit('/admin/permissions')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deletePermission();
    }

    /** @test */
    public function an_admin_can_filter_permissions_by_end_date()
    {
        $this->admin->grantPermission('permissions-list');

        $this->createPermission();

        $past = Carbon::now()->subDays(7)->format('Y-m-d');
        $future = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/permissions')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', $future)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/permissions', $this->permissionModel)
                ->assertSee($this->permissionName);
        });

        $this->deletePermission();
    }

    /** @test */
    public function an_admin_can_clear_permission_filters()
    {
        $this->admin->grantPermission('permissions-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions/?search=list&guard=web&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('guard')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/permissions/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('guard')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_a_name_when_creating_a_permission()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->grantPermission('permissions-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
                ->clickLink('Add New')
                ->type('#group-input', $this->permissionGroup)
                ->type('#label-input', $this->permissionLabel)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });
    }

    /** @test */
    public function it_requires_a_name_when_updating_a_permission()
    {
        $this->admin->grantPermission('permissions-list');
        $this->admin->grantPermission('permissions-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/permissions')
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
    protected function createPermission()
    {
        $this->permissionModel = Permission::create([
            'guard' => 'web',
            'name' => $this->permissionName,
            'label' => $this->permissionLabel,
            'group' => $this->permissionGroup,
        ]);
    }

    /**
     * @return void
     */
    protected function deletePermission()
    {
        Permission::whereName($this->permissionName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deletePermissionModified()
    {
        Permission::whereName($this->permissionNameModified)->first()->delete();
    }
}
