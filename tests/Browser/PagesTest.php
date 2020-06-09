<?php

namespace Varbox\Tests\Browser;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use Varbox\Models\Page;

class PagesTest extends TestCase
{
    /**
     * @var Page
     */
    protected $pageModel;

    /**
     * @var string
     */
    protected $pageName = 'Test Page Name';

    /**
     * @var string
     */
    protected $pageSlug = 'test-page-name';

    /**
     * @var string
     */
    protected $pageType = 'test-type';

    /**
     * @var string
     */
    protected $pageNameModified = 'Test Page Name Modified';

    /**
     * @var string
     */
    protected $pageSlugModified = 'test-page-name-modified';

    /**
     * @return void
     * @throws \Exception
     */
    public function setUp(): void
    {
        parent::setUp();

        cache()->forget('first_tree_load');
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('varbox.pages.types', [
            $this->pageType => [
                'controller' => 'PagesController',
                'action' => 'show',
                'locations' => [
                    'header', 'content', 'footer'
                ]
            ]
        ]);
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages')
                ->assertPathIs('/admin/pages')
                ->assertSee('Pages');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('pages-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages')
                ->assertPathIs('/admin/pages')
                ->assertSee('Pages');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('pages-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages')
                ->assertSee('Unauthorized')
                ->assertDontSee('Pages');
        });
    }

    /** @test */
    public function an_admin_can_view_the_export_button_if_it_is_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages')
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_can_view_the_export_button_if_it_has_permission()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages')
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_export_button_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->revokePermission('pages-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages')
                ->assertSourceMissing('button-export');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages')
                ->clickLink('Add New')
                ->assertPathIs('/admin/pages/create')
                ->assertSee('Add Page');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages')
                ->clickLink('Add New')
                ->assertPathIs('/admin/pages/create')
                ->assertSee('Add Page');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->revokePermission('pages-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages')
                ->assertDontSee('Add New')
                ->visit('/admin/pages/create')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Page');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/pages', $this->pageModel)
                ->waitFor('#root_id_anchor')
                ->click('#root_id_anchor')
                ->waitFor('.js-TreeTable')
                ->clickEditRecordButton($this->pageName)
                ->assertPathIs('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('Edit Page');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-edit');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/pages', $this->pageModel)
                ->waitFor('#root_id_anchor')
                ->click('#root_id_anchor')
                ->waitFor('.js-TreeTable')
                ->clickEditRecordButton($this->pageName)
                ->assertPathIs('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('Edit Page');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->revokePermission('pages-edit');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/pages', $this->pageModel)
                ->assertSourceMissing('button-edit')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Page');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_create_a_page()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/create')
                ->type('#name-input', $this->pageName)
                ->type('#slug-input', $this->pageSlug)
                ->typeSelect2('#type-input', $this->pageTypeFormatted())
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/pages')
                ->assertSee('The record was successfully created!')
                ->waitFor('#root_id_anchor')
                ->click('#root_id_anchor')
                ->waitFor('.js-TreeTable')
                ->assertSee($this->pageName);
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_create_a_page_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/create')
                ->type('#name-input', $this->pageName)
                ->type('#slug-input', $this->pageSlug)
                ->typeSelect2('#type-input', $this->pageTypeFormatted())
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/pages/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_create_a_page_and_continue_editing_it()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-add');
        $this->admin->grantPermission('pages-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/create')
                ->type('#name-input', $this->pageName)
                ->type('#slug-input', $this->pageSlug)
                ->typeSelect2('#type-input', $this->pageTypeFormatted())
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/pages/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#name-input', $this->pageName)
                ->assertSee($this->pageTypeFormatted());
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_update_a_page()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-edit');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->type('#name-input', $this->pageNameModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/pages')
                ->assertSee('The record was successfully updated!')
                ->waitFor('#root_id_anchor')
                ->click('#root_id_anchor')
                ->waitFor('.js-TreeTable')
                ->assertSee($this->pageNameModified);
        });

        $this->deletePageModified();
    }

    /** @test */
    public function an_admin_can_update_a_page_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-edit');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->type('#name-input', $this->pageNameModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#name-input', $this->pageNameModified);
        });

        $this->deletePageModified();
    }

    /** @test */
    public function an_admin_can_delete_a_page_if_it_has_permission()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-delete');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->resize(1200, 1200)->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/pages/', $this->pageModel)
                ->waitFor('#root_id_anchor')
                ->click('#root_id_anchor')
                ->waitFor('.js-TreeTable')
                ->assertSee($this->pageName)
                ->clickDeleteRecordButton($this->pageName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/pages/', $this->pageModel)
                ->assertDontSee($this->pageName);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->revokePermission('pages-delete');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages')
                ->assertSourceMissing('button-delete');
        });

        $this->deletePage();
    }

    /** @test */
    public function it_requires_a_name_when_creating_a_page()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/create')
                ->type('#slug-input', $this->pageSlug)
                ->typeSelect2('#type-input', $this->pageTypeFormatted())
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });
    }

    /** @test */
    public function it_requires_a_unique_name_when_creating_a_page()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-add');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/create')
                ->type('#name-input', $this->pageName)
                ->type('#slug-input', $this->pageSlug)
                ->typeSelect2('#type-input', $this->pageTypeFormatted())
                ->press('Save')
                ->waitForText('The name has already been taken')
                ->assertSee('The name has already been taken');
        });

        $this->deletePage();
    }

    /** @test */
    public function it_requires_a_type_when_creating_a_page()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/create')
                ->type('#name-input', $this->pageName)
                ->type('#slug-input', $this->pageSlug)
                ->press('Save')
                ->waitForText('The type field is required')
                ->assertSee('The type field is required');
        });
    }

    /** @test */
    public function it_requires_a_name_when_updating_a_page()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-edit');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->type('#name-input', '')
                ->type('#slug-input', $this->pageSlug)
                ->typeSelect2('#type-input', $this->pageTypeFormatted())
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });

        $this->deletePage();
    }

    /** @test */
    public function it_requires_a_unique_name_when_updating_a_page()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-edit');

        $this->createPage();
        $this->createPageModified();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->type('#name-input', $this->pageName)
                ->type('#slug-input', $this->pageSlug)
                ->typeSelect2('#type-input', $this->pageTypeFormatted())
                ->press('Save')
                ->waitForText('The name has already been taken')
                ->assertSee('The name has already been taken');
        });

        $this->deletePageModified();
        $this->deletePage();
    }

    /** @test */
    public function it_requires_a_type_when_updating_a_page()
    {
        $this->admin->grantPermission('pages-list');
        $this->admin->grantPermission('pages-edit');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->resize(1250, 2500)->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->type('#name-input', $this->pageName)
                ->type('#slug-input', $this->pageSlug)
                ->click('.select2-selection__clear')
                ->press('Save')
                ->waitForText('The type field is required')
                ->assertSee('The type field is required');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_create_a_drafted_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/create')
                ->type('#name-input', $this->pageName)
                ->type('#slug-input', $this->pageSlug)
                ->typeSelect2('#type-input', $this->pageTypeFormatted())
                ->clickSaveDraftRecordButton()
                ->pause(500)
                ->assertPathBeginsWith('/admin/pages/edit')
                ->assertSee('The draft was successfully created!')
                ->assertInputValue('#name-input', $this->pageName)
                ->assertSee('This record is currently drafted');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_create_a_drafted_page_if_it_has_permission()
    {
        $this->admin->grantPermission('pages-add');
        $this->admin->grantPermission('pages-edit');
        $this->admin->grantPermission('pages-draft');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/create')
                ->type('#name-input', $this->pageName)
                ->type('#slug-input', $this->pageSlug)
                ->typeSelect2('#type-input', $this->pageTypeFormatted())
                ->clickSaveDraftRecordButton()
                ->pause(500)
                ->assertPathBeginsWith('/admin/pages/edit')
                ->assertSee('The draft was successfully created!')
                ->assertInputValue('#name-input', $this->pageName)
                ->assertSee('This record is currently drafted');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_cannot_create_a_drafted_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('pages-add');
        $this->admin->grantPermission('pages-edit');
        $this->admin->revokePermission('pages-draft');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/create')
                ->assertDontSee('Save As Draft');
        });
    }

    /** @test */
    public function an_admin_can_save_a_page_as_draft_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->type('#name-input', $this->pageNameModified)
                ->clickSaveDraftRecordButton()
                ->pause(500)
                ->assertPathIs('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('The draft was successfully updated!')
                ->assertInputValue('#name-input', $this->pageNameModified)
                ->assertSee('This record is currently drafted');
        });

        $this->deletePageModified();
    }

    /** @test */
    public function an_admin_can_save_a_page_as_draft_if_it_has_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->grantPermission('pages-draft');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->type('#name-input', $this->pageNameModified)
                ->clickSaveDraftRecordButton()
                ->pause(500)
                ->assertPathIs('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('The draft was successfully updated!')
                ->assertInputValue('#name-input', $this->pageNameModified)
                ->assertSee('This record is currently drafted');
        });

        $this->deletePageModified();
    }

    /** @test */
    public function an_admin_cannot_save_a_page_as_draft_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->revokePermission('pages-draft');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->assertDontSee('Save As Draft');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_publish_a_drafted_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createPage();

        $this->pageModel = $this->pageModel->saveAsDraft();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->clickPublishRecordButton()
                ->pause(500)
                ->assertPathIs('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('The draft was successfully published!')
                ->assertDontSee('This record is currently drafted')
                ->assertDontSee('Publish Draft');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_publish_a_drafted_page_if_it_has_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->grantPermission('pages-publish');

        $this->createPage();

        $this->pageModel = $this->pageModel->saveAsDraft();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->clickPublishRecordButton()
                ->pause(500)
                ->assertPathIs('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('The draft was successfully published!')
                ->assertDontSee('This record is currently drafted')
                ->assertDontSee('Publish Draft');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_cannot_publish_a_drafted_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->revokePermission('pages-publish');

        $this->createPage();

        $this->pageModel = $this->pageModel->saveAsDraft();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('This record is currently drafted')
                ->assertDontSee('Publish Draft');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_duplicate_a_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->clickDuplicateRecordButton()
                ->pause(500)
                ->assertPathIsNot('/admin/pages/edit/' . $this->pageModel->id)
                ->assertPathBeginsWith('/admin/pages/edit')
                ->assertSee('The record was successfully duplicated')
                ->assertInputValue('#name-input', $this->pageName . ' (1)');
        });

        $this->deletePage();
        $this->deleteDuplicatedPage();
    }

    /** @test */
    public function an_admin_can_duplicate_a_page_if_it_has_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->grantPermission('pages-duplicate');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->clickDuplicateRecordButton()
                ->pause(500)
                ->assertPathIsNot('/admin/pages/edit/' . $this->pageModel->id)
                ->assertPathBeginsWith('/admin/pages/edit')
                ->assertSee('The record was successfully duplicated')
                ->assertInputValue('#name-input', $this->pageName . ' (1)');
        });

        $this->deletePage();
        $this->deleteDuplicatedPage();
    }

    /** @test */
    public function an_admin_cannot_duplicate_a_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->revokePermission('pages-duplicate');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->assertDontSee('Duplicate');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_see_page_revisions_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('Revisions Info')
                ->openRevisionsContainer()
                ->pause(500)
                ->assertSee('There are no revisions for this record');
        });

        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('Revisions Info')
                ->openRevisionsContainer()
                ->pause(500)
                ->assertSee('No User')
                ->assertSourceHas('button-view-revision')
                ->assertSourceHas('button-rollback-revision')
                ->assertSourceHas('button-delete-revision');
        });

        $this->deletePageModified();
    }

    /** @test */
    public function an_admin_can_see_page_revisions_if_it_is_has_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->grantPermission('revisions-list');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('Revisions Info')
                ->openRevisionsContainer()
                ->pause(500)
                ->assertSee('There are no revisions for this record');
        });

        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->assertSee('Revisions Info')
                ->openRevisionsContainer()
                ->pause(500)
                ->assertSee('No User')
                ->assertDontSee('There are no revisions for this record');
        });

        $this->deletePageModified();
    }

    /** @test */
    public function an_admin_cannot_see_page_revisions_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->revokePermission('revisions-list');

        $this->createPage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->assertDontSee('Revisions Info');
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_view_a_page_revision_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createPage();
        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->openRevisionsContainer()
                ->pause(500)
                ->clickViewRevisionButton()
                ->assertPathBeginsWith('/admin/pages/revision')
                ->assertSee('You are currently viewing a revision of the record')
                ->assertSee('Page Revision')
                ->assertInputValue('#name-input', $this->pageName);
        });

        $this->deletePageModified();
    }

    /** @test */
    public function an_admin_can_view_a_page_revision_if_it_has_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->grantPermission('revisions-list');

        $this->createPage();
        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->openRevisionsContainer()
                ->pause(500)
                ->clickViewRevisionButton()
                ->assertPathBeginsWith('/admin/pages/revision')
                ->assertSee('You are currently viewing a revision of the record')
                ->assertSee('Page Revision')
                ->assertInputValue('#name-input', $this->pageName);
        });

        $this->deletePageModified();
    }

    /** @test */
    public function an_admin_can_rollback_a_page_revision_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createPage();
        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->resize(1600, 1600)->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->screenshot('1')
                ->openRevisionsContainer()
                ->screenshot('2')
                ->clickRollbackRevisionButton()
                ->screenshot('3')
                ->pause(500)
                ->assertSee('The revision was successfully rolled back')
                ->assertPathIs('/admin/pages/edit/' . $this->pageModel->id)
                ->assertInputValue('#name-input', $this->pageName);
        });

        $this->deletePage();
        $this->createPage();
        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->openRevisionsContainer()
                ->clickViewRevisionButton()
                ->pressRollbackRevisionButton()
                ->pause(500)
                ->assertSee('The revision was successfully rolled back')
                ->assertPathIs('/admin/pages/edit/' . $this->pageModel->id)
                ->assertInputValue('#name-input', $this->pageName);
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_can_rollback_a_page_revision_if_it_has_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->grantPermission('revisions-list');
        $this->admin->grantPermission('revisions-rollback');

        $this->createPage();
        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->openRevisionsContainer()
                ->clickRollbackRevisionButton()
                ->pause(500)
                ->assertSee('The revision was successfully rolled back')
                ->assertPathIs('/admin/pages/edit/' . $this->pageModel->id)
                ->assertInputValue('#name-input', $this->pageName);
        });

        $this->deletePage();
        $this->createPage();
        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->openRevisionsContainer()
                ->clickViewRevisionButton()
                ->pressRollbackRevisionButton()
                ->pause(500)
                ->assertSee('The revision was successfully rolled back')
                ->assertPathIs('/admin/pages/edit/' . $this->pageModel->id)
                ->assertInputValue('#name-input', $this->pageName);
        });

        $this->deletePage();
    }

    /** @test */
    public function an_admin_cannot_rollback_a_page_revision_if_doesnt_have_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->grantPermission('revisions-list');
        $this->admin->revokePermission('revisions-rollback');

        $this->createPage();
        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->openRevisionsContainer()
                ->assertSourceMissing('class="button-rollback-revision');
        });

        $this->deletePageModified();
        $this->createPage();
        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->openRevisionsContainer()
                ->clickViewRevisionButton()
                ->assertDontSee('Rollback Revision')
                ->assertSourceMissing('class="button-rollback-revision');
        });

        $this->deletePageModified();
    }

    /** @test */
    public function an_admin_can_delete_a_page_revision_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createPage();
        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->openRevisionsContainer()
                ->clickDeleteRevisionButton()
                ->pause(500)
                ->assertSee('There are no revisions for this record');
        });

        $this->deletePageModified();
    }

    /** @test */
    public function an_admin_can_delete_a_page_revision_if_it_has_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->grantPermission('revisions-list');
        $this->admin->grantPermission('revisions-delete');

        $this->createPage();
        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->openRevisionsContainer()
                ->clickDeleteRevisionButton()
                ->pause(500)
                ->assertSee('There are no revisions for this record');
        });

        $this->deletePageModified();
    }

    /** @test */
    public function an_admin_cannot_delete_a_page_revision_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('pages-edit');
        $this->admin->grantPermission('revisions-list');
        $this->admin->revokePermission('revisions-delete');

        $this->createPage();
        $this->updatePage();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/pages/edit/' . $this->pageModel->id)
                ->openRevisionsContainer()
                ->assertSourceMissing('class="button-delete-revision');
        });

        $this->deletePageModified();
    }

    /**
     * @return void
     */
    protected function createPage()
    {
        $this->pageModel = Page::create([
            'name' => $this->pageName,
            'slug' => $this->pageSlug,
            'type' => $this->pageType,
        ]);
    }

    /**
     * @return void
     */
    protected function updatePage()
    {
        $this->pageModel->fresh()->update([
            'name' => $this->pageNameModified
        ]);
    }

    /**
     * @return void
     */
    protected function createPageModified()
    {
        $this->pageModel = Page::create([
            'name' => $this->pageNameModified,
            'slug' => $this->pageSlugModified,
            'type' => $this->pageType,
        ]);
    }

    /**
     * @return void
     */
    protected function deletePage()
    {
        Page::withDrafts()->whereName($this->pageName)
            ->first()->delete();
    }

    /**
     * @return void
     */
    protected function deletePageModified()
    {
        Page::withDrafts()->whereName($this->pageNameModified)
            ->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteDuplicatedPage()
    {
        Page::withDrafts()->whereName($this->pageName . ' (1)')
            ->first()->delete();
    }

    /**
     * @return string
     */
    protected function pageTypeFormatted()
    {
        return Str::title(str_replace(['_', '-', '.'], ' ', $this->pageType));
    }
}
