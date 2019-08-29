<?php

namespace Varbox\Tests\Browser;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;
use Varbox\Models\Menu;

class MenusTest extends TestCase
{
    /**
     * @var Menu
     */
    protected $menuModel;

    /**
     * @var string
     */
    protected $menuName = 'Test Menu Name';

    /**
     * @var string
     */
    protected $menuType = 'url';

    /**
     * @var string
     */
    protected $menuUrl = 'https://test-url.tld';

    /**
     * @var string
     */
    protected $menuLocation = 'header';

    /**
     * @var string
     */
    protected $menuNameModified = 'Test Menu Name Modified';

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

        $app['config']->set('varbox.menus.locations', [
            $this->menuLocation
        ]);
    }

    /** @test */
    public function an_admin_can_view_the_locations_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation)
                ->assertPathIs('/admin/menus/' . $this->menuLocation)
                ->assertSee('Menu Locations');
        });
    }

    /** @test */
    public function an_admin_can_view_the_locations_page_if_it_has_permission()
    {
        $this->admin->grantPermission('menus-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation)
                ->assertPathIs('/admin/menus/' . $this->menuLocation)
                ->assertSee('Menu Locations');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_locations_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('menus-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation)
                ->assertSee('Unauthorized')
                ->assertDontSee('Menu Locations');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/locations')
                ->clickLink(Str::title($this->menuLocation) . ' Location')
                ->assertPathIs('/admin/menus/' . $this->menuLocation)
                ->assertSee('Menus');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('menus-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/locations')
                ->clickLink(Str::title($this->menuLocation) . ' Location')
                ->assertPathIs('/admin/menus/' . $this->menuLocation)
                ->assertSee('Menus');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('menus-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation)
                ->assertSee('Unauthorized')
                ->assertDontSee('Menus');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation)
                ->clickLink('Add New')
                ->assertPathIs('/admin/menus/' . $this->menuLocation . '/create')
                ->assertSee('Add Menu');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation)
                ->clickLink('Add New')
                ->assertPathIs('/admin/menus/' . $this->menuLocation . '/create')
                ->assertSee('Add Menu');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->revokePermission('menus-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation . '/create')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Menu');
        });
    }









    /** @test */
    public function an_admin_can_view_the_edit_menu_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createMenu();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/menus/' . $this->menuLocation, $this->menuModel)
                ->waitFor('#root_id_anchor')
                ->click('#root_id_anchor')
                ->waitFor('.js-TreeTable')
                ->clickEditRecordButton($this->menuName)
                ->assertPathIs('/admin/menus/' . $this->menuLocation . '/edit/' . $this->menuModel->id)
                ->assertSee('Edit Menu');
        });

        $this->deleteMenu();
    }

    /** @test */
    public function an_admin_can_view_the_edit_menu_if_it_has_permission()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-edit');

        $this->createMenu();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/menus/' . $this->menuLocation, $this->menuModel)
                ->waitFor('#root_id_anchor')
                ->click('#root_id_anchor')
                ->waitFor('.js-TreeTable')
                ->clickEditRecordButton($this->menuName)
                ->assertPathIs('/admin/menus/' . $this->menuLocation . '/edit/' . $this->menuModel->id)
                ->assertSee('Edit Menu');
        });

        $this->deleteMenu();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_menu_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->revokePermission('menus-edit');

        $this->createMenu();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/menus/' . $this->menuLocation, $this->menuModel)
                ->assertSourceMissing('button-edit')
                ->visit('/admin/menus/' . $this->menuLocation . '/edit/' . $this->menuModel->id)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Menu');
        });

        $this->deleteMenu();
    }

    /**
     * @return void
     */
    protected function createMenu()
    {
        $this->menuModel = Menu::create([
            'location' => $this->menuLocation,
            'type' => $this->menuType,
            'name' => $this->menuName,
            'url' => $this->menuUrl,
        ]);
    }

    /**
     * @return void
     */
    protected function updateMenu()
    {
        $this->menuModel->fresh()->update([
            'name' => $this->menuNameModified
        ]);
    }

    /**
     * @return void
     */
    protected function createMenuModified()
    {
        $this->menuModel = Menu::create([
            'location' => $this->menuLocation,
            'type' => $this->menuType,
            'name' => $this->menuNameModified,
            'url' => $this->menuUrl,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteMenu()
    {
        Menu::whereName($this->menuName)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteMenuModified()
    {
        Menu::whereName($this->menuNameModified)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteDuplicatedMenu()
    {
        Menu::whereName($this->menuName . ' (1)')->first()->delete();
    }

    /**
     * @return string
     */
    protected function menuTypeFormatted()
    {
        return Str::title(str_replace(['_', '-', '.'], ' ', $this->menuType));
    }
}
