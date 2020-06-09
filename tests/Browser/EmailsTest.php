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
                    'username', 'home_url'
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
    public function an_admin_can_view_the_export_button_if_it_is_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_can_view_the_export_button_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_export_button_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->revokePermission('emails-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->assertSourceMissing('button-export');
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
                ->assertDontSee('Add New')
                ->visit('/admin/emails/create')
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
                ->clickEditRecordButton($this->emailName)
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
                ->clickEditRecordButton($this->emailName)
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
                ->assertSourceMissing('button-edit')
                ->visit('/admin/emails/edit/' . $this->emailModel->id)
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
                ->typeSelect2('#type-input', $this->emailTypeFormatted())
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
                ->typeSelect2('#type-input', $this->emailTypeFormatted())
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
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->type('#name-input', $this->emailName)
                ->typeSelect2('#type-input', $this->emailTypeFormatted())
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/emails/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#name-input', $this->emailName)
                ->assertSee($this->emailTypeFormatted());
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
                ->clickEditRecordButton($this->emailName)
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
                ->clickEditRecordButton($this->emailName)
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
    public function an_admin_can_delete_an_email_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-delete');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->assertSee($this->emailName)
                ->clickDeleteRecordButton($this->emailName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/emails/', $this->emailModel)
                ->assertDontSee($this->emailName);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_an_email_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->revokePermission('emails-delete');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->assertSourceMissing('button-delete');
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

    /** @test */
    public function it_requires_a_name_when_creating_an_email()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->typeSelect2('#type-input', $this->emailTypeFormatted())
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });
    }

    /** @test */
    public function it_requires_a_unique_name_when_creating_an_email()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-add');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->type('#name-input', $this->emailName)
                ->typeSelect2('#type-input', $this->emailTypeFormatted())
                ->press('Save')
                ->waitForText('The name has already been taken')
                ->assertSee('The name has already been taken');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function it_requires_a_type_when_creating_an_email()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->type('#name-input', $this->emailName)
                ->press('Save')
                ->waitForText('The type field is required')
                ->assertSee('The type field is required');
        });
    }

    /** @test */
    public function it_requires_a_name_when_updating_an_email()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->type('#name-input', '')
                ->typeSelect2('#type-input', $this->emailTypeFormatted())
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function it_requires_a_unique_name_when_updating_an_email()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');

        $this->createEmail();
        $this->createEmailModified();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->type('#name-input', $this->emailNameModified)
                ->typeSelect2('#type-input', $this->emailTypeFormatted())
                ->press('Save')
                ->waitForText('The name has already been taken')
                ->assertSee('The name has already been taken');
        });

        $this->deleteEmailModified();
        $this->deleteEmail();
    }

    /** @test */
    public function it_requires_a_type_when_updating_an_email()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->resize(1250, 2500)->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->type('#name-input', $this->emailName)
                ->click('.select2-selection__clear')
                ->press('Save')
                ->waitForText('The type field is required')
                ->assertSee('The type field is required');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_create_a_drafted_email_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->type('#name-input', $this->emailName)
                ->typeSelect2('#type-input', $this->emailTypeFormatted())
                ->clickSaveDraftRecordButton()
                ->pause(500)
                ->assertPathBeginsWith('/admin/emails/edit')
                ->assertSee('The draft was successfully created!')
                ->assertInputValue('#name-input', $this->emailName)
                ->assertSee('This record is currently drafted');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_create_a_drafted_email_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-add');
        $this->admin->grantPermission('emails-edit');
        $this->admin->grantPermission('emails-draft');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->type('#name-input', $this->emailName)
                ->typeSelect2('#type-input', $this->emailTypeFormatted())
                ->clickSaveDraftRecordButton()
                ->pause(500)
                ->assertPathBeginsWith('/admin/emails/edit')
                ->assertSee('The draft was successfully created!')
                ->assertInputValue('#name-input', $this->emailName)
                ->assertSee('This record is currently drafted');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_cannot_create_a_drafted_email_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-add');
        $this->admin->grantPermission('emails-edit');
        $this->admin->revokePermission('emails-draft');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickLink('Add New')
                ->assertDontSee('Save As Draft');
        });
    }

    /** @test */
    public function an_admin_can_save_an_email_as_draft_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->type('#name-input', $this->emailNameModified)
                ->clickSaveDraftRecordButton()
                ->pause(500)
                ->assertPathIs('/admin/emails/edit/' . $this->emailModel->id)
                ->assertSee('The draft was successfully updated!')
                ->assertInputValue('#name-input', $this->emailNameModified)
                ->assertSee('This record is currently drafted');
        });

        $this->deleteEmailModified();
    }

    /** @test */
    public function an_admin_can_save_an_email_as_draft_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->grantPermission('emails-draft');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->type('#name-input', $this->emailNameModified)
                ->clickSaveDraftRecordButton()
                ->pause(500)
                ->assertPathIs('/admin/emails/edit/' . $this->emailModel->id)
                ->assertSee('The draft was successfully updated!')
                ->assertInputValue('#name-input', $this->emailNameModified)
                ->assertSee('This record is currently drafted');
        });

        $this->deleteEmailModified();
    }

    /** @test */
    public function an_admin_cannot_save_an_email_as_draft_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->revokePermission('emails-draft');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->assertDontSee('Save As Draft');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_publish_a_drafted_email_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createEmail();

        $this->emailModel = $this->emailModel->saveAsDraft();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->clickPublishRecordButton()
                ->pause(500)
                ->assertPathIs('/admin/emails/edit/' . $this->emailModel->id)
                ->assertSee('The draft was successfully published!')
                ->assertDontSee('This record is currently drafted')
                ->assertDontSee('Publish Draft');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_publish_a_drafted_email_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->grantPermission('emails-publish');

        $this->createEmail();

        $this->emailModel = $this->emailModel->saveAsDraft();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->clickPublishRecordButton()
                ->pause(500)
                ->assertPathIs('/admin/emails/edit/' . $this->emailModel->id)
                ->assertSee('The draft was successfully published!')
                ->assertDontSee('This record is currently drafted')
                ->assertDontSee('Publish Draft');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_cannot_publish_a_drafted_email_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->revokePermission('emails-publish');

        $this->createEmail();

        $this->emailModel = $this->emailModel->saveAsDraft();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->assertSee('This record is currently drafted')
                ->assertDontSee('Publish Draft');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_duplicate_an_email_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->clickDuplicateRecordButton()
                ->pause(500)
                ->assertPathIsNot('/admin/emails/edit/' . $this->emailModel->id)
                ->assertPathBeginsWith('/admin/emails/edit')
                ->assertSee('The record was successfully duplicated')
                ->assertInputValue('#name-input', $this->emailName . ' (1)');
        });

        $this->deleteEmail();
        $this->deleteDuplicatedEmail();
    }

    /** @test */
    public function an_admin_can_duplicate_an_email_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->grantPermission('emails-duplicate');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->clickDuplicateRecordButton()
                ->pause(500)
                ->assertPathIsNot('/admin/emails/edit/' . $this->emailModel->id)
                ->assertPathBeginsWith('/admin/emails/edit')
                ->assertSee('The record was successfully duplicated')
                ->assertInputValue('#name-input', $this->emailName . ' (1)');
        });

        $this->deleteEmail();
        $this->deleteDuplicatedEmail();
    }

    /** @test */
    public function an_admin_cannot_duplicate_an_email_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->revokePermission('emails-duplicate');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->assertDontSee('Duplicate');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_see_email_revisions_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->assertSee('Revisions Info')
                ->openRevisionsContainer()
                ->pause(500)
                ->assertSee('There are no revisions for this record');
        });

        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->assertSee('Revisions Info')
                ->openRevisionsContainer()
                ->pause(500)
                ->assertSee('No User')
                ->assertSourceHas('button-view-revision')
                ->assertSourceHas('button-rollback-revision')
                ->assertSourceHas('button-delete-revision');
        });

        $this->deleteEmailModified();
    }

    /** @test */
    public function an_admin_can_see_email_revisions_if_it_is_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->grantPermission('revisions-list');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->assertSee('Revisions Info')
                ->openRevisionsContainer()
                ->pause(500)
                ->assertSee('There are no revisions for this record');
        });

        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->assertSee('Revisions Info')
                ->openRevisionsContainer()
                ->pause(500)
                ->assertSee('No User')
                ->assertDontSee('There are no revisions for this record');
        });

        $this->deleteEmailModified();
    }

    /** @test */
    public function an_admin_cannot_see_email_revisions_if_it_is_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->revokePermission('revisions-list');

        $this->createEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailName)
                ->assertDontSee('Revisions Info');
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_view_an_email_revision_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createEmail();
        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->openRevisionsContainer()
                ->pause(500)
                ->clickViewRevisionButton()
                ->assertPathBeginsWith('/admin/emails/revision')
                ->assertSee('You are currently viewing a revision of the record')
                ->assertSee('Email Revision')
                ->assertInputValue('#name-input', $this->emailName);
        });

        $this->deleteEmailModified();
    }

    /** @test */
    public function an_admin_can_view_an_email_revision_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->grantPermission('revisions-list');

        $this->createEmail();
        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->openRevisionsContainer()
                ->pause(500)
                ->clickViewRevisionButton()
                ->assertPathBeginsWith('/admin/emails/revision')
                ->assertSee('You are currently viewing a revision of the record')
                ->assertSee('Email Revision')
                ->assertInputValue('#name-input', $this->emailName);
        });

        $this->deleteEmailModified();
    }

    /** @test */
    public function an_admin_can_rollback_an_email_revision_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createEmail();
        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->openRevisionsContainer()
                ->clickRollbackRevisionButton()
                ->pause(500)
                ->assertSee('The revision was successfully rolled back')
                ->assertPathIs('/admin/emails/edit/' . $this->emailModel->id)
                ->assertInputValue('#name-input', $this->emailName);
        });

        $this->deleteEmail();
        $this->createEmail();
        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->openRevisionsContainer()
                ->clickViewRevisionButton()
                ->pressRollbackRevisionButton()
                ->pause(500)
                ->assertSee('The revision was successfully rolled back')
                ->assertPathIs('/admin/emails/edit/' . $this->emailModel->id)
                ->assertInputValue('#name-input', $this->emailName);
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_can_rollback_an_email_revision_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->grantPermission('revisions-list');
        $this->admin->grantPermission('revisions-rollback');

        $this->createEmail();
        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->openRevisionsContainer()
                ->clickRollbackRevisionButton()
                ->pause(500)
                ->assertSee('The revision was successfully rolled back')
                ->assertPathIs('/admin/emails/edit/' . $this->emailModel->id)
                ->assertInputValue('#name-input', $this->emailName);
        });

        $this->deleteEmail();
        $this->createEmail();
        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->openRevisionsContainer()
                ->clickViewRevisionButton()
                ->pressRollbackRevisionButton()
                ->pause(500)
                ->assertSee('The revision was successfully rolled back')
                ->assertPathIs('/admin/emails/edit/' . $this->emailModel->id)
                ->assertInputValue('#name-input', $this->emailName);
        });

        $this->deleteEmail();
    }

    /** @test */
    public function an_admin_cannot_rollback_an_email_revision_if_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->grantPermission('revisions-list');
        $this->admin->revokePermission('revisions-rollback');

        $this->createEmail();
        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->openRevisionsContainer()
                ->assertSourceMissing('class="button-rollback-revision');
        });

        $this->deleteEmailModified();
        $this->createEmail();
        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->openRevisionsContainer()
                ->clickViewRevisionButton()
                ->assertDontSee('Rollback Revision')
                ->assertSourceMissing('class="button-rollback-revision');
        });

        $this->deleteEmailModified();
    }

    /** @test */
    public function an_admin_can_delete_an_email_revision_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createEmail();
        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->openRevisionsContainer()
                ->clickDeleteRevisionButton()
                ->pause(500)
                ->assertSee('There are no revisions for this record');
        });

        $this->deleteEmailModified();
    }

    /** @test */
    public function an_admin_can_delete_an_email_revision_if_it_has_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->grantPermission('revisions-list');
        $this->admin->grantPermission('revisions-delete');

        $this->createEmail();
        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->openRevisionsContainer()
                ->clickDeleteRevisionButton()
                ->pause(500)
                ->assertSee('There are no revisions for this record');
        });

        $this->deleteEmailModified();
    }

    /** @test */
    public function an_admin_cannot_delete_an_email_revision_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('emails-list');
        $this->admin->grantPermission('emails-edit');
        $this->admin->grantPermission('revisions-list');
        $this->admin->revokePermission('revisions-delete');

        $this->createEmail();
        $this->updateEmail();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/emails')
                ->clickEditRecordButton($this->emailNameModified)
                ->openRevisionsContainer()
                ->assertSourceMissing('class="button-delete-revision');
        });

        $this->deleteEmailModified();
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
    protected function updateEmail()
    {
        $this->emailModel->fresh()->update([
            'name' => $this->emailNameModified
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
        Email::withDrafts()->whereName($this->emailName)
            ->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteEmailModified()
    {
        Email::withDrafts()->whereName($this->emailNameModified)
            ->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteDuplicatedEmail()
    {
        Email::withDrafts()->whereName($this->emailName . ' (1)')
            ->first()->delete();
    }

    /**
     * @return string
     */
    protected function emailTypeFormatted()
    {
        return Str::title(str_replace(['_', '-', '.'], ' ', $this->emailType));
    }
}
