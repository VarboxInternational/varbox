<?php

namespace Varbox\Tests\Browser;

use Varbox\Models\Language;

class LanguagesTest extends TestCase
{
    /**
     * @var Language
     */
    protected $languageModel;

    /**
     * @var string
     */
    protected $languageName = 'Test Language Name';
    protected $languageCode = 'TCC';

    /**
     * @var string
     */
    protected $languageNameModified = 'Test Language Name Modified';

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->assertPathIs('/admin/languages')
                ->assertSee('Languages');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('languages-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->assertPathIs('/admin/languages')
                ->assertSee('Languages');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('languages-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->assertSee('Unauthorized')
                ->assertDontSee('Languages');
        });
    }

    /** @test */
    public function an_admin_can_view_the_export_button_if_it_is_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_can_view_the_export_button_if_it_has_permission()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_export_button_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->revokePermission('languages-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->assertSourceMissing('button-export');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->clickLink('Add New')
                ->assertPathIs('/admin/languages/create')
                ->assertSee('Add Language');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->clickLink('Add New')
                ->assertPathIs('/admin/languages/create')
                ->assertSee('Add Language');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->revokePermission('languages-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->assertDontSee('Add New')
                ->visit('/admin/languages/create')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Language');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createLanguage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/languages', $this->languageModel)
                ->clickEditRecordButton($this->languageName)
                ->assertPathIs('/admin/languages/edit/' . $this->languageModel->id)
                ->assertSee('Edit Language');
        });

        $this->deleteLanguage();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-edit');

        $this->createLanguage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/languages', $this->languageModel)
                ->clickEditRecordButton($this->languageName)
                ->assertPathIs('/admin/languages/edit/' . $this->languageModel->id)
                ->assertSee('Edit Language');
        });

        $this->deleteLanguage();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->revokePermission('languages-edit');

        $this->createLanguage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/languages', $this->languageModel)
                ->assertSourceMissing('button-edit')
                ->visit('/admin/languages/edit/' . $this->languageModel->id)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Language');
        });

        $this->deleteLanguage();
    }

    /** @test */
    public function an_admin_can_create_a_language()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->clickLink('Add New')
                ->type('#name-input', $this->languageName)
                ->type('#code-input', $this->languageCode)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/languages')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/languages/', new Language)
                ->assertSee($this->languageName)
                ->assertSee($this->languageCode);
        });

        $this->deleteLanguage();
    }

    /** @test */
    public function an_admin_can_create_a_language_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->clickLink('Add New')
                ->type('#name-input', $this->languageName)
                ->type('#code-input', $this->languageCode)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/languages/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deleteLanguage();
    }

    /** @test */
    public function an_admin_can_create_a_language_and_continue_editing_it()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-add');
        $this->admin->grantPermission('languages-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->clickLink('Add New')
                ->type('#name-input', $this->languageName)
                ->type('#code-input', $this->languageCode)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/languages/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#name-input', $this->languageName)
                ->assertInputValue('#code-input', $this->languageCode);
        });

        $this->deleteLanguage();
    }

    /** @test */
    public function an_admin_can_update_a_language()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-edit');

        $this->createLanguage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/languages', $this->languageModel)
                ->clickEditRecordButton($this->languageName)
                ->type('#name-input', $this->languageNameModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/languages')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/languages', $this->languageModel)
                ->assertSee($this->languageNameModified);
        });

        $this->deleteLanguageModified();
    }

    /** @test */
    public function an_admin_can_update_a_language_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-edit');

        $this->createLanguage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/languages', $this->languageModel)
                ->clickEditRecordButton($this->languageName)
                ->type('#name-input', $this->languageNameModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/languages/edit/' . $this->languageModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#name-input', $this->languageNameModified);
        });

        $this->deleteLanguageModified();
    }

    /** @test */
    public function an_admin_can_delete_a_language_if_it_has_permission()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-delete');

        $this->createLanguage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/languages/', $this->languageModel)
                ->assertSee($this->languageName)
                ->assertSee($this->languageCode)
                ->clickDeleteRecordButton($this->languageName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/languages/', $this->languageModel)
                ->assertDontSee($this->languageName)
                ->assertDontSee($this->languageCode);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_language_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->revokePermission('languages-delete');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->assertSourceMissing('button-delete');
        });
    }

    /** @test */
    public function an_admin_can_filter_languages_by_keyword()
    {
        $this->admin->grantPermission('languages-list');

        $this->createLanguage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->filterRecordsByText('#search-input', $this->languageName)
                ->assertQueryStringHas('search', $this->languageName)
                ->assertSee($this->languageName)
                ->assertRecordsCount(1);
        });

        $this->deleteLanguage();
    }

    /** @test */
    public function an_admin_can_filter_languages_by_start_date()
    {
        $this->admin->grantPermission('languages-list');

        $this->createLanguage();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/languages', $this->languageModel)
                ->assertSee($this->languageName)
                ->visit('/admin/languages')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteLanguage();
    }

    /** @test */
    public function an_admin_can_filter_languages_by_end_date()
    {
        $this->admin->grantPermission('languages-list');

        $this->createLanguage();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/languages')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', $future)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/languages', $this->languageModel)
                ->assertSee($this->languageName);
        });

        $this->deleteLanguage();
    }

    /** @test */
    public function an_admin_can_clear_language_filters()
    {
        $this->admin->grantPermission('languages-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages/?search=a&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/languages/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_a_name_when_creating_a_language()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->clickLink('Add New')
                ->type('#code-input', $this->languageCode)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });
    }

    /** @test */
    public function it_requires_a_code_when_creating_a_language()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->clickLink('Add New')
                ->type('#name-input', $this->languageName)
                ->press('Save')
                ->waitForText('The code field is required')
                ->assertSee('The code field is required');
        });
    }

    /** @test */
    public function it_requires_a_name_when_updating_a_language()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-edit');

        $this->createLanguage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->click('.button-edit')
                ->type('#name-input', '')
                ->type('#code-input', $this->languageCode)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });

        $this->deleteLanguage();
    }

    /** @test */
    public function it_requires_a_code_when_updating_a_language()
    {
        $this->admin->grantPermission('languages-list');
        $this->admin->grantPermission('languages-edit');

        $this->createLanguage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/languages')
                ->click('.button-edit')
                ->type('#code-input', '')
                ->type('#name-input', $this->languageName)
                ->press('Save')
                ->waitForText('The code field is required')
                ->assertSee('The code field is required');
        });

        $this->deleteLanguage();
    }

    /**
     * @return void
     */
    protected function createLanguage()
    {
        $this->languageModel = Language::create([
            'name' => $this->languageName,
            'code' => $this->languageCode,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteLanguage()
    {
        Language::whereName($this->languageName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteLanguageModified()
    {
        Language::whereName($this->languageNameModified)->first()->delete();
    }
}
