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
        Email::whereName($this->emailName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteEmailModified()
    {
        Email::whereName($this->emailNameModified)->first()->delete();
    }
}
