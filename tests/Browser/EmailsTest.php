<?php

namespace Varbox\Tests\Browser;

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
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('varbox.emails.types', [
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
        Email::whereName($this->emailName)->first()->forceDelete();
    }

    /**
     * @return void
     */
    protected function deleteEmailModified()
    {
        Email::whereName($this->emailNameModified)->first()->forceDelete();
    }
}
