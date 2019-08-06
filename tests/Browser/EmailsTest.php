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
                ->deleteRecord($this->emailName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->assertSee($this->emailName);
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

    /** @test */
    public function an_admin_can_force_delete_an_email_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-soft-delete');
        $this->admin->grantPermission('emails-force-delete');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->assertSee($this->emailName)
                ->deleteRecord($this->emailName)
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->deleteRecord($this->emailName)
                ->assertSee('The record was successfully force deleted!')
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->assertDontSee($this->emailName);
        });
    }

    /** @test */
    public function an_admin_cannot_force_delete_an_email_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-soft-delete');
        $this->admin->revokePermission('emails-force-delete');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->assertSee($this->emailName)
                ->deleteRecord($this->emailName)
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->deleteRecord($this->emailName)
                ->assertDontSee('The record was successfully force deleted!')
                ->assertSee('Unauthorized');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_restore_an_email_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-soft-delete');
        $this->admin->grantPermission('emails-restore');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->assertSee($this->emailName)
                ->deleteRecord($this->emailName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->assertSee($this->emailName)
                ->restoreRecord($this->emailName)
                ->assertSee('The record was successfully restored!');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_cannot_restore_an_email_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-soft-delete');
        $this->admin->revokePermission('emails-restore');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->deleteRecord($this->emailName)
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->restoreRecord($this->emailName)
                ->assertDontSee('The record was successfully restored!')
                ->assertSee('Unauthorized');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_filter_emails_by_keyword()
    {
        $this->admin->grantPermission('emails-list');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->filterRecordsByText('#search-input', $this->emailName)
                ->assertQueryStringHas('search', $this->emailName)
                ->assertSee($this->emailName)
                ->assertRecordsCount(1)
                ->visit('/admin/emails')
                ->filterRecordsByText('#search-input', $this->emailNameModified)
                ->assertQueryStringHas('search', $this->emailNameModified)
                ->assertDontSee($this->emailName)
                ->assertSee('No records found');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_filter_emails_by_type()
    {
        $this->admin->grantPermission('emails-list');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->filterRecordsBySelect('#type-input', $this->emailTypeFormatted())
                ->assertQueryStringHas('type', $this->emailType)
                ->assertRecordsCount(1)
                ->assertSee($this->emailName);
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_filter_emails_by_published()
    {
        $this->admin->grantPermission('emails-list');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->filterRecordsBySelect('#drafted-input', 'Yes')
                ->assertQueryStringHas('drafted', 1)
                ->assertRecordsCount(1)
                ->assertSee($this->emailName)
                ->visit('/admin/emails')
                ->filterRecordsBySelect('#drafted-input', 'No')
                ->assertQueryStringHas('drafted', 2)
                ->assertSee('No records found');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_filter_emails_by_trashed()
    {
        $this->admin->grantPermission('emails-list');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->filterRecordsBySelect('#trashed-input', 'No')
                ->assertQueryStringHas('trashed', 2)
                ->assertRecordsCount(1)
                ->assertSee($this->emailName)
                ->visit('/admin/emails')
                ->filterRecordsBySelect('#trashed-input', 'Yes')
                ->assertQueryStringHas('trashed', 1)
                ->assertSee('No records found');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_filter_emails_by_start_date()
    {
        $this->admin->grantPermission('emails-list');

        $this->createEmail();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->visitLastPage('/admin/emails', $this->emailModel)
                ->assertSee($this->emailName)
                ->visit('/admin/emails')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_filter_emails_by_end_date()
    {
        $this->admin->grantPermission('emails-list');

        $this->createEmail();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/emails')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', $future)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/emails', $this->emailModel)
                ->assertSee($this->emailName);
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_clear_email_filters()
    {
        $this->admin->grantPermission('emails-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails/?search=list&type=test&drafted=1&trashed=2&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('type')
                ->assertQueryStringHas('drafted')
                ->assertQueryStringHas('trashed')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/emails/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('type')
                ->assertQueryStringMissing('drafted')
                ->assertQueryStringMissing('trashed')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
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
    protected function createEmailModified()
    {
        $this->emailModel = Email::create([
            'name' => $this->emailNameModified,
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
