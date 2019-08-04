<?php

namespace Varbox\Tests\Browser;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use Varbox\Models\Email;

class EmailsTest extends TestCase
{
    /**
     * @var Email
     */
    protected $emailModel;

    /**
     * @var string
     */
    protected $emailName = 'Test Email Name';
    protected $emailType = 'test-type';
    protected $emailSubject = 'Test Email Subject';
    protected $emailMessage = 'Test Email Message';


    /**
     * @var string
     */
    protected $emailNameModified = 'Test Email Name Modified';

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('varbox.emails.types', [
            $this->emailType => [
                'class' => 'App\Mail\TestMail',
                'view' => 'emails.test_mail',
                'variables' => [
                    'first_name', 'last_name', 'full_name'
                ],
            ]
        ]);
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->assertPathIs('/admin/emails')
                ->assertSee('Emails');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->assertPathIs('/admin/emails')
                ->assertSee('Emails');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('emails-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->assertSee('Unauthorized')
                ->assertDontSee('Emails');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->assertPathIs('/admin/emails/create')
                ->assertSee('Add Email');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->assertPathIs('/admin/emails/create')
                ->assertSee('Add Email');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->revokePermission('emails-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Email');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/emails', $this->emailModel)
                ->clickEditButton($this->emailName)
                ->assertPathIs('/admin/emails/edit/' . $this->emailModel->id)
                ->assertSee('Edit Email');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/emails', $this->emailModel)
                ->clickEditButton($this->emailName)
                ->assertPathIs('/admin/emails/edit/' . $this->emailModel->id)
                ->assertSee('Edit Email');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->revokePermission('emails-edit');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/emails', $this->emailModel)
                ->clickEditButton($this->emailName)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Email');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_create_an_email()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->type('#name-input', $this->emailName)
                ->select2('#type-input', $this->emailTypeFormatted())
                ->type('#data-subject--input', $this->emailSubject)
                ->froala('data-message--input', $this->emailMessage)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/emails')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/emails/', new Email)
                ->assertSee($this->emailName);
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_create_an_email_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->type('#name-input', $this->emailName)
                ->select2('#type-input', $this->emailTypeFormatted())
                ->type('#data-subject--input', $this->emailSubject)
                ->froala('data-message--input', $this->emailMessage)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/emails/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_create_an_email_and_continue_editing_it()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-add');
        $this->admin->grantPermission('emails-edit');

        $this->browse(function ($browser) {
            $browser->resize(2000, 2000)->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->type('#name-input', $this->emailName)
                ->select2('#type-input', $this->emailTypeFormatted())
                ->type('#data-subject--input', $this->emailSubject)
                ->froala('data-message--input', $this->emailMessage)
                ->screenshot('aaa')
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/emails/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#name-input', $this->emailName)
                ->assertSee($this->emailTypeFormatted())
                ->assertInputValue('#data-subject--input', $this->emailSubject)
                ->assertSee($this->emailMessage);
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_update_an_email()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/emails', $this->emailModel)
                ->clickEditButton($this->emailName)
                ->type('#name-input', $this->emailNameModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/emails')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/emails', $this->emailModel)
                ->assertSee($this->emailNameModified);
        });

        $this->deleteEmailModified();
    }

    /** @test */
    public function an_admin_can_update_an_email_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/emails', $this->emailModel)
                ->clickEditButton($this->emailName)
                ->type('#name-input', $this->emailNameModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/emails/edit/' . $this->emailModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#name-input', $this->emailNameModified);
        });

        $this->deleteEmailModified();
    }

    /** @test */
    public function an_admin_can_soft_delete_an_email_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-soft-delete');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->assertSee($this->emailName)
                ->assertSourceMissing('button-restore')
                ->deleteRecord($this->emailName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->assertSee($this->emailName)
                ->assertSourceHas('button-restore');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_cannot_soft_delete_an_email_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->revokePermission('emails-soft-delete');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->deleteAnyRecord()
                ->assertDontSee('The record was successfully deleted!')
                ->assertSee('Unauthorized');
        });

        $this->deleteEmail();
    }

    /**
     * @return void
     */
    protected function createEmail()
    {
        $this->emailModel = Email::create([
            'name' => $this->emailName,
            'type' => $this->emailType,
            'data' => [
                'subject' => $this->emailSubject,
                'message' => $this->emailMessage,
            ],
        ]);
    }

    /**
     * @return void
     */
    protected function deleteEmail()
    {
        Email::withTrashed()->whereName($this->emailName)->first()->forceDelete();
    }

    /**
     * @return void
     */
    protected function deleteEmailModified()
    {
        Email::withTrashed()->whereName($this->emailNameModified)->first()->forceDelete();
    }

    /**
     * @return string
     */
    protected function emailTypeFormatted()
    {
        return Str::title(str_replace(['_', '-', '.'], ' ', $this->emailType));
    }
}
