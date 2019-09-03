<?php

namespace Varbox\Tests\Browser;

use Varbox\Models\Translation;

class TranslationsTest extends TestCase
{
    /**
     * @var Translation
     */
    protected $translationModel;

    /**
     * @var string
     */
    protected $translationKey = 'test_key';
    protected $translationGroup = 'test_group';
    protected $translationLocale = 'test-locale';
    protected $translationValue = 'test value';

    /**
     * @var string
     */
    protected $translationValueModified = 'test value modified';

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/translations')
                ->assertPathIs('/admin/translations')
                ->assertSee('Translations');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('translations-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/translations')
                ->assertPathIs('/admin/translations')
                ->assertSee('Translations');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('translations-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/translations')
                ->assertSee('Unauthorized')
                ->assertDontSee('Translations');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createTranslation();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/translations', $this->translationModel)
                ->clickEditRecordButton($this->translationKey)
                ->assertPathIs('/admin/translations/edit/' . $this->translationModel->id)
                ->assertSee('Edit Translation');
        });

        $this->deleteTranslation();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('translations-list');
        $this->admin->grantPermission('translations-edit');

        $this->createTranslation();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/translations', $this->translationModel)
                ->clickEditRecordButton($this->translationKey)
                ->assertPathIs('/admin/translations/edit/' . $this->translationModel->id)
                ->assertSee('Edit Translation');
        });

        $this->deleteTranslation();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('translations-list');
        $this->admin->revokePermission('translations-edit');

        $this->createTranslation();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/translations', $this->translationModel)
                ->assertSourceMissing('button-edit')
                ->visit('/admin/translations/edit/' . $this->translationModel->id)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Translation');
        });

        $this->deleteTranslation();
    }

    /** @test */
    public function an_admin_can_update_a_translation()
    {
        $this->admin->grantPermission('translations-list');
        $this->admin->grantPermission('translations-edit');

        $this->createTranslation();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/translations', $this->translationModel)
                ->clickEditRecordButton($this->translationKey)
                ->type('#value-input', $this->translationValueModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/translations')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/translations', $this->translationModel)
                ->assertSee($this->translationValueModified);
        });

        $this->deleteTranslationModified();
    }

    /** @test */
    public function an_admin_can_update_a_translation_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('translations-list');
        $this->admin->grantPermission('translations-edit');

        $this->createTranslation();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/translations', $this->translationModel)
                ->clickEditRecordButton($this->translationKey)
                ->type('#value-input', $this->translationValueModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/translations/edit/' . $this->translationModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#value-input', $this->translationValueModified);
        });

        $this->deleteTranslationModified();
    }

    /** @test */
    public function an_admin_can_delete_a_translation_if_it_has_permission()
    {
        $this->admin->grantPermission('translations-list');
        $this->admin->grantPermission('translations-delete');

        $this->createTranslation();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/translations/', $this->translationModel)
                ->assertSee($this->translationKey)
                ->assertSee($this->translationGroup)
                ->assertSee(strtoupper($this->translationLocale))
                ->clickDeleteRecordButton($this->translationKey)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/translations/', $this->translationModel)
                ->assertDontSee($this->translationKey)
                ->assertDontSee($this->translationGroup)
                ->assertDontSee(strtoupper($this->translationLocale));
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_translation_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('translations-list');
        $this->admin->revokePermission('translations-delete');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/translations')
                ->assertSourceMissing('button-delete');
        });
    }

    /** @test */
    public function an_admin_can_filter_translations_by_keyword()
    {
        $this->admin->grantPermission('translations-list');

        $this->createTranslation();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/translations')
                ->filterRecordsByText('#search-input', $this->translationKey)
                ->assertQueryStringHas('search', $this->translationKey)
                ->assertSee($this->translationKey)
                ->assertRecordsCount(1);
        });

        $this->deleteTranslation();
    }

    /** @test */
    public function an_admin_can_filter_translations_by_start_date()
    {
        $this->admin->grantPermission('translations-list');

        $this->createTranslation();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/translations')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', $past)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/translations', $this->translationModel)
                ->assertSee($this->translationKey)
                ->visit('/admin/translations')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', $future)
                ->assertSee('No records found');
        });

        $this->deleteTranslation();
    }

    /** @test */
    public function an_admin_can_filter_translations_by_end_date()
    {
        $this->admin->grantPermission('translations-list');

        $this->createTranslation();

        $past = today()->subDays(7)->format('Y-m-d');
        $future = today()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/translations')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', $past)
                ->assertSee('No records found')
                ->visit('/admin/translations')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', $future)
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/translations', $this->translationModel)
                ->assertSee($this->translationKey);
        });

        $this->deleteTranslation();
    }

    /** @test */
    public function an_admin_can_clear_translation_filters()
    {
        $this->admin->grantPermission('translations-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/translations/?search=a&locale=en&group=b&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('locale')
                ->assertQueryStringHas('group')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/translations/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('locale')
                ->assertQueryStringMissing('group')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_a_value_when_updating_a_translation()
    {
        $this->admin->grantPermission('translations-list');
        $this->admin->grantPermission('translations-edit');

        $this->createTranslation();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/translations')
                ->click('.button-edit')
                ->type('#value-input', '')
                ->press('Save')
                ->waitForText('The value field is required')
                ->assertSee('The value field is required');
        });
    }

    /**
     * @return void
     */
    protected function createTranslation()
    {
        $this->translationModel = Translation::create([
            'locale' => $this->translationLocale,
            'key' => $this->translationKey,
            'group' => $this->translationGroup,
            'value' => $this->translationValue,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteTranslation()
    {
        Translation::where('value', $this->translationValue)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteTranslationModified()
    {
        Translation::where('value', $this->translationValueModified)->first()->delete();
    }
}
