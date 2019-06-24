<?php

namespace Varbox\Tests\Browser;

use Carbon\Carbon;
use Varbox\Models\User;

class UsersTest extends TestCase
{
    /**
     * @var User
     */
    protected $userModel;

    /**
     * @var string
     */
    protected $userEmail = 'test-user-email@mail.com';
    protected $userPassword = 'test_user_password';
    protected $userFirstName = 'Test User First Name';
    protected $userLastName = 'Test User Last Name';

    /**
     * @var string
     */
    protected $userEmailModified = 'test-user-email-modified@mail.com';
    protected $useEmailInvalid = 'test-user-email';

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->assertPathIs('/admin/users')
                ->assertSee('Users');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('users-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->assertPathIs('/admin/users')
                ->assertSee('Users');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('users-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->assertSee('Unauthorized')
                ->assertDontSee('Users');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->clickLink('Add New')
                ->assertPathIs('/admin/users/create')
                ->assertSee('Add User');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->clickLink('Add New')
                ->assertPathIs('/admin/users/create')
                ->assertSee('Add User');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->revokePermission('users-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->clickLink('Add New')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add User');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createUser();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users', $this->userModel)
                ->clickEditButton($this->userEmail)
                ->assertPathIs('/admin/users/edit/' . $this->userModel->id)
                ->assertSee('Edit User');
        });

        $this->deleteUser();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-edit');

        $this->createUser();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users', $this->userModel)
                ->clickEditButton($this->userEmail)
                ->assertPathIs('/admin/users/edit/' . $this->userModel->id)
                ->assertSee('Edit User');
        });

        $this->deleteUser();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->revokePermission('users-edit');

        $this->createUser();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users', $this->userModel)
                ->clickEditButton($this->userEmail)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit User');
        });

        $this->deleteUser();
    }

    /** @test */
    public function an_admin_can_create_a_user()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->clickLink('Add New')
                ->type('#email-input', $this->userEmail)
                ->type('#password-input', $this->userPassword)
                ->type('#password_confirmation-input', $this->userPassword)
                ->type('#first_name-input', $this->userFirstName)
                ->type('#last_name-input', $this->userLastName)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/users')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/users/', new User)
                ->assertSee($this->userEmail)
                ->assertSee($this->userFirstName . ' ' . $this->userLastName);
        });

        $this->deleteUser();
    }

    /** @test */
    public function an_admin_can_create_a_user_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->clickLink('Add New')
                ->type('#email-input', $this->userEmail)
                ->type('#password-input', $this->userPassword)
                ->type('#password_confirmation-input', $this->userPassword)
                ->type('#first_name-input', $this->userFirstName)
                ->type('#last_name-input', $this->userLastName)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/users/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deleteUser();
    }

    /** @test */
    public function an_admin_can_create_a_user_and_continue_editing_it()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-add');
        $this->admin->grantPermission('users-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->clickLink('Add New')
                ->type('#email-input', $this->userEmail)
                ->type('#password-input', $this->userPassword)
                ->type('#password_confirmation-input', $this->userPassword)
                ->type('#first_name-input', $this->userFirstName)
                ->type('#last_name-input', $this->userLastName)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/users/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#email-input', $this->userEmail)
                ->assertInputValue('#first_name-input', $this->userFirstName)
                ->assertInputValue('#last_name-input', $this->userLastName);
        });

        $this->deleteUser();
    }

    /** @test */
    public function an_admin_can_update_a_user()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-edit');

        $this->createUser();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users', $this->userModel)
                ->clickEditButton($this->userEmail)
                ->type('#email-input', $this->userEmailModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/users')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/users', $this->userModel)
                ->assertSee($this->userEmailModified);
        });

        $this->deleteUserModified();
    }

    /** @test */
    public function an_admin_can_update_a_user_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-edit');

        $this->createUser();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users', $this->userModel)
                ->clickEditButton($this->userEmail)
                ->type('#email-input', $this->userEmailModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/users/edit/' . $this->userModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#email-input', $this->userEmailModified);
        });

        $this->deleteUserModified();
    }

    /** @test */
    public function an_admin_can_delete_a_user_if_it_has_permission()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-delete');

        $this->createUser();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/users/', $this->userModel)
                ->assertSee($this->userEmail)
                ->deleteRecord($this->userEmail)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/users/', $this->userModel)
                ->assertDontSee($this->userEmail);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_user_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->revokePermission('users-delete');

        $this->createUser();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->deleteAnyRecord()
                ->assertDontSee('The record was successfully deleted!')
                ->assertSee('Unauthorized');
        });

        $this->deleteUser();
    }

    /** @test */
    public function an_admin_can_filter_users_by_keyword()
    {
        $this->admin->grantPermission('users-list');

        $this->createUser();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->filterRecordsByText('#search-input', $this->userEmail)
                ->assertQueryStringHas('search', $this->userEmail)
                ->assertSee($this->userEmail)
                ->assertRecordsCount(1);
        });

        $this->deleteUser();
    }

    /** @test */
    public function an_admin_can_filter_users_by_active()
    {
        $this->admin->grantPermission('users-list');

        $this->createUser();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->filterRecordsBySelect('#active-input', 'No')
                ->assertQueryStringHas('active', 0)
                ->assertRecordsCount(1)
                ->assertSee($this->userEmail)
                ->visit('/admin/users')
                ->filterRecordsBySelect('#active-input', 'Yes')
                ->assertQueryStringHas('active', 1)
                ->assertSee('No records found');
        });

        $this->deleteUser();
    }

    /** @test */
    public function an_admin_can_filter_users_by_start_date()
    {
        $this->admin->grantPermission('users-list');

        $this->createUser();

        $past = Carbon::now()->subDays(7)->format('Y-m-d');
        $future = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/users', $this->userModel)
                ->assertSee($this->userEmail)
                ->visit('/admin/users')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteUser();
    }

    /** @test */
    public function an_admin_can_filter_users_by_end_date()
    {
        $this->admin->grantPermission('users-list');

        $this->createUser();

        $past = Carbon::now()->subDays(7)->format('Y-m-d');
        $future = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/users')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', $future)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/users', $this->userModel)
                ->assertSee($this->userEmail);
        });

        $this->deleteUser();
    }

    /** @test */
    public function an_admin_can_clear_user_filters()
    {
        $this->admin->grantPermission('users-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users/?search=list&active=1&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('active')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/users/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('active')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_an_email_when_creating_a_user()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->clickLink('Add New')
                ->type('#password-input', $this->userPassword)
                ->type('#password_confirmation-input', $this->userPassword)
                ->type('#first_name-input', $this->userFirstName)
                ->type('#last_name-input', $this->userLastName)
                ->press('Save')
                ->waitForText('The email field is required')
                ->assertSee('The email field is required');
        });
    }

    /** @test */
    public function it_requires_a_first_name_when_creating_a_user()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->clickLink('Add New')
                ->type('#email-input', $this->userEmail)
                ->type('#password-input', $this->userPassword)
                ->type('#password_confirmation-input', $this->userPassword)
                ->type('#last_name-input', $this->userLastName)
                ->press('Save')
                ->waitForText('The first name field is required')
                ->assertSee('The first name field is required');
        });
    }

    /** @test */
    public function it_requires_a_last_name_when_creating_a_user()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->clickLink('Add New')
                ->type('#email-input', $this->userEmail)
                ->type('#password-input', $this->userPassword)
                ->type('#password_confirmation-input', $this->userPassword)
                ->type('#first_name-input', $this->userFirstName)
                ->press('Save')
                ->waitForText('The last name field is required')
                ->assertSee('The last name field is required');
        });
    }

    /** @test */
    public function it_requires_an_email_when_updating_a_user()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-add');

        $this->createUser();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->clickEditButton($this->userEmail)
                ->type('#email-input', '')
                ->type('#password-input', $this->userPassword)
                ->type('#password_confirmation-input', $this->userPassword)
                ->type('#first_name-input', $this->userFirstName)
                ->type('#last_name-input', $this->userLastName)
                ->press('Save')
                ->waitForText('The email field is required')
                ->assertSee('The email field is required');
        });

        $this->deleteUser();
    }

    /** @test */
    public function it_requires_a_first_name_when_updating_a_user()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-add');

        $this->createUser();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->clickEditButton($this->userEmail)
                ->type('#email-input', $this->userEmail)
                ->type('#password-input', $this->userPassword)
                ->type('#password_confirmation-input', $this->userPassword)
                ->type('#first_name-input', '')
                ->type('#last_name-input', $this->userLastName)
                ->press('Save')
                ->waitForText('The first name field is required')
                ->assertSee('The first name field is required');
        });

        $this->deleteUser();
    }

    /** @test */
    public function it_requires_a_last_name_when_updating_a_user()
    {
        $this->admin->grantPermission('users-list');
        $this->admin->grantPermission('users-add');

        $this->createUser();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/users')
                ->clickEditButton($this->userEmail)
                ->type('#email-input', $this->userEmail)
                ->type('#password-input', $this->userPassword)
                ->type('#password_confirmation-input', $this->userPassword)
                ->type('#first_name-input', $this->userFirstName)
                ->type('#last_name-input', '')
                ->press('Save')
                ->waitForText('The last name field is required')
                ->assertSee('The last name field is required');
        });

        $this->deleteUser();
    }

    /**
     * @return void
     */
    protected function createUser()
    {
        $this->userModel = User::create([
            'email' => $this->userEmail,
            'password' => bcrypt($this->userPassword),
            'first_name' => $this->userFirstName,
            'last_name' => $this->userLastName,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteUser()
    {
        User::whereEmail($this->userEmail)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteUserModified()
    {
        User::whereEmail($this->userEmailModified)->first()->delete();
    }
}
