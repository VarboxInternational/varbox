<?php

namespace Varbox\Tests\Browser;

use Carbon\Carbon;
use Varbox\Models\User;

class AdminsTest extends TestCase
{
    /**
     * @var User
     */
    protected $adminModel;

    /**
     * @var string
     */
    protected $adminName = 'Test Admin Name';
    protected $adminEmail = 'test-admin-email@mail.com';
    protected $adminPassword = 'test_admin_password';

    /**
     * @var string
     */
    protected $adminEmailModified = 'test-admin-email-modified@mail.com';
    protected $useEmailInvalid = 'test-admin-email';

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->assertPathIs('/admin/admins')
                ->assertSee('Admins');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('admins-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->assertPathIs('/admin/admins')
                ->assertSee('Admins');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('admins-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->assertSee('401')
                ->assertDontSee('Admins');
        });
    }

    /** @test */
    public function an_admin_can_view_the_export_button_if_it_is_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_can_view_the_export_button_if_it_has_permission()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_export_button_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->revokePermission('admins-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->assertSourceMissing('button-export');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->clickLink('Add New')
                ->assertPathIs('/admin/admins/create')
                ->assertSee('Add Admin');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->clickLink('Add New')
                ->assertPathIs('/admin/admins/create')
                ->assertSee('Add Admin');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->revokePermission('admins-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->assertDontSee('Add New')
                ->visit('/admin/admins/create')
                ->assertSee('401')
                ->assertDontSee('Add Admin');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createAdmin();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/admins', $this->adminModel)
                ->clickEditRecordButton($this->adminEmail)
                ->assertPathIs('/admin/admins/edit/' . $this->adminModel->id)
                ->assertSee('Edit Admin');
        });

        $this->deleteAdmin();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-edit');

        $this->createAdmin();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/admins', $this->adminModel)
                ->clickEditRecordButton($this->adminEmail)
                ->assertPathIs('/admin/admins/edit/' . $this->adminModel->id)
                ->assertSee('Edit Admin');
        });

        $this->deleteAdmin();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->revokePermission('admins-edit');

        $this->createAdmin();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/admins', $this->adminModel)
                ->assertSourceMissing('button-edit')
                ->visit('/admin/admins/edit/' . $this->adminModel->id)
                ->assertSee('401')
                ->assertDontSee('Edit Admin');
        });

        $this->deleteAdmin();
    }

    /** @test */
    public function an_admin_can_create_an_admin()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->clickLink('Add New')
                ->type('#email-input', $this->adminEmail)
                ->typeSelect2('#roles-input', 'Admin')
                ->type('#password-input', $this->adminPassword)
                ->type('#password_confirmation-input', $this->adminPassword)
                ->type('#name-input', $this->adminName)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/admins')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/admins/', new User)
                ->assertSee($this->adminEmail)
                ->assertSee($this->adminName);
        });

        $this->deleteAdmin();
    }

    /** @test */
    public function an_admin_can_create_an_admin_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->clickLink('Add New')
                ->type('#email-input', $this->adminEmail)
                ->type('#password-input', $this->adminPassword)
                ->type('#password_confirmation-input', $this->adminPassword)
                ->type('#name-input', $this->adminName)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/admins/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deleteAdmin();
    }

    /** @test */
    public function an_admin_can_create_an_admin_and_continue_editing_it()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-add');
        $this->admin->grantPermission('admins-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->clickLink('Add New')
                ->type('#email-input', $this->adminEmail)
                ->type('#password-input', $this->adminPassword)
                ->type('#password_confirmation-input', $this->adminPassword)
                ->type('#name-input', $this->adminName)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/admins/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#email-input', $this->adminEmail)
                ->assertInputValue('#name-input', $this->adminName);
        });

        $this->deleteAdmin();
    }

    /** @test */
    public function an_admin_can_update_an_admin()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-edit');

        $this->createAdmin();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/admins', $this->adminModel)
                ->clickEditRecordButton($this->adminEmail)
                ->type('#email-input', $this->adminEmailModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/admins')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/admins', $this->adminModel)
                ->assertSee($this->adminEmailModified);
        });

        $this->deleteAdminModified();
    }

    /** @test */
    public function an_admin_can_update_an_admin_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-edit');

        $this->createAdmin();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/admins', $this->adminModel)
                ->clickEditRecordButton($this->adminEmail)
                ->type('#email-input', $this->adminEmailModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/admins/edit/' . $this->adminModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#email-input', $this->adminEmailModified);
        });

        $this->deleteAdminModified();
    }

    /** @test */
    public function an_admin_can_delete_an_admin_if_it_has_permission()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-delete');

        $this->createAdmin();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/admins/', $this->adminModel)
                ->assertSee($this->adminEmail)
                ->clickDeleteRecordButton($this->adminEmail)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/admins/', $this->adminModel)
                ->assertDontSee($this->adminEmail);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_an_admin_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->revokePermission('admins-delete');

        $this->createAdmin();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->assertSourceMissing('button-delete');
        });

        $this->deleteAdmin();
    }

    /** @test */
    public function an_admin_can_filter_admins_by_keyword()
    {
        $this->admin->grantPermission('admins-list');

        $this->createAdmin();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->filterRecordsByText('#search-input', $this->adminEmail)
                ->assertQueryStringHas('search', $this->adminEmail)
                ->assertSee($this->adminEmail)
                ->assertRecordsCount(1);
        });

        $this->deleteAdmin();
    }

    /** @test */
    public function an_admin_can_filter_admins_by_active()
    {
        $this->admin->grantPermission('admins-list');

        $this->createAdmin();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->filterRecordsBySelect('#active-input', 'No')
                ->assertQueryStringHas('active', 0)
                ->assertRecordsCount(1)
                ->assertSee($this->adminEmail)
                ->visit('/admin/admins')
                ->filterRecordsBySelect('#active-input', 'Yes')
                ->assertQueryStringHas('active', 1)
                ->assertDontSee($this->adminEmail);
        });

        $this->deleteAdmin();
    }

    /** @test */
    public function an_admin_can_filter_admins_by_start_date()
    {
        $this->admin->grantPermission('admins-list');

        $this->createAdmin();

        $past = Carbon::now()->subDays(7)->format('Y-m-d');
        $future = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/admins', $this->adminModel)
                ->assertSee($this->adminEmail)
                ->visit('/admin/admins')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteAdmin();
    }

    /** @test */
    public function an_admin_can_filter_admins_by_end_date()
    {
        $this->admin->grantPermission('admins-list');

        $this->createAdmin();

        $past = Carbon::now()->subDays(7)->format('Y-m-d');
        $future = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/admins')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', $future)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/admins', $this->adminModel)
                ->assertSee($this->adminEmail);
        });

        $this->deleteAdmin();
    }

    /** @test */
    public function an_admin_can_clear_admin_filters()
    {
        $this->admin->grantPermission('admins-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins/?search=list&active=1&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('active')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/admins/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('active')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_an_email_when_creating_an_admin()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->clickLink('Add New')
                ->type('#password-input', $this->adminPassword)
                ->type('#password_confirmation-input', $this->adminPassword)
                ->type('#name-input', $this->adminName)
                ->press('Save')
                ->waitForText('The email field is required')
                ->assertSee('The email field is required');
        });
    }

    /** @test */
    public function it_requires_a_name_when_creating_an_admin()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->clickLink('Add New')
                ->type('#email-input', $this->adminEmail)
                ->type('#password-input', $this->adminPassword)
                ->type('#password_confirmation-input', $this->adminPassword)
                ->type('#name-input', '')
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });
    }

    /** @test */
    public function it_requires_an_email_when_updating_an_admin()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-edit');

        $this->createAdmin();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->clickEditRecordButton($this->adminEmail)
                ->type('#email-input', '')
                ->type('#password-input', $this->adminPassword)
                ->type('#password_confirmation-input', $this->adminPassword)
                ->type('#name-input', $this->adminName)
                ->press('Save')
                ->waitForText('The email field is required')
                ->assertSee('The email field is required');
        });

        $this->deleteAdmin();
    }

    /** @test */
    public function it_requires_a_name_when_updating_an_admin()
    {
        $this->admin->grantPermission('admins-list');
        $this->admin->grantPermission('admins-edit');

        $this->createAdmin();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/admins')
                ->clickEditRecordButton($this->adminEmail)
                ->type('#email-input', $this->adminEmail)
                ->type('#password-input', $this->adminPassword)
                ->type('#password_confirmation-input', $this->adminPassword)
                ->type('#name-input', '')
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });

        $this->deleteAdmin();
    }

    /**
     * @return void
     */
    protected function createAdmin()
    {
        $this->adminModel = User::create([
            'name' => $this->adminName,
            'email' => $this->adminEmail,
            'password' => bcrypt($this->adminPassword),
        ]);

        $this->adminModel->assignRoles('Admin');
        $this->adminModel = $this->adminModel->fresh();
    }

    /**
     * @return void
     */
    protected function deleteAdmin()
    {
        User::whereEmail($this->adminEmail)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteAdminModified()
    {
        User::whereEmail($this->adminEmailModified)->first()->delete();
    }
}
