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

    /**
     * @return void
     */
    protected function createMenu()
    {
        $this->menuModel = Menu::create([
            'name' => $this->menuName,
            'type' => $this->menuType,
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
            'name' => $this->menuNameModified,
            'type' => $this->menuType,
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
