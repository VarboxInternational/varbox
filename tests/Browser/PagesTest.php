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
    protected $pageType = 'test-type';

    /**
     * @var string
     */
    protected $pageNameModified = 'Test Page Name Modified';

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



    /**
     * @return void
     */
    protected function createPage()
    {
        $this->pageModel = Page::create([
            'name' => $this->pageName,
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
