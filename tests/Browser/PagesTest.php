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
    public function an_admin_can_create_an_page()
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
    public function an_admin_can_create_an_page_and_stay_to_create_another_one()
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
    public function an_admin_can_create_an_page_and_continue_editing_it()
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
    public function an_admin_can_update_an_page()
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
    public function an_admin_can_update_an_page_and_stay_to_continue_editing_id()
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
    public function an_admin_can_delete_an_page_if_it_has_permission()
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
    public function an_admin_cannot_delete_an_page_if_it_doesnt_have_permission()
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
