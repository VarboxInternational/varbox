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
                ->visit('/admin/menus/locations')
                ->assertPathIs('/admin/menus/locations')
                ->assertSee('Menu Locations');
        });
    }

    /** @test */
    public function an_admin_can_view_the_locations_page_if_it_has_permission()
    {
        $this->admin->grantPermission('menus-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/locations')
                ->assertPathIs('/admin/menus/locations')
                ->assertSee('Menu Locations');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_locations_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('menus-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/locations')
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
    public function an_admin_can_view_the_export_button_if_it_is_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation)
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_can_view_the_export_button_if_it_has_permission()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation)
                ->assertSourceHas('button-export');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_export_button_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->revokePermission('menus-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation)
                ->assertSourceMissing('button-export');
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

    /** @test */
    public function an_admin_can_create_a_menu()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation . '/create')
                ->type('#name-input', $this->menuName)
                ->typeSelect2('#type-input', $this->menuTypeFormatted())
                ->waitFor('#url-input')
                ->type('#url-input', $this->menuUrl)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/menus/' . $this->menuLocation)
                ->assertSee('The record was successfully created!')
                ->waitFor('#root_id_anchor')
                ->click('#root_id_anchor')
                ->waitFor('.js-TreeTable')
                ->assertSee($this->menuName);
        });

        $this->deleteMenu();
    }

    /** @test */
    public function an_admin_can_create_a_menu_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation . '/create')
                ->type('#name-input', $this->menuName)
                ->typeSelect2('#type-input', $this->menuTypeFormatted())
                ->waitFor('#url-input')
                ->type('#url-input', $this->menuUrl)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/menus/' . $this->menuLocation . '/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deleteMenu();
    }

    /** @test */
    public function an_admin_can_create_a_menu_and_continue_editing_it()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-add');
        $this->admin->grantPermission('menus-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation . '/create')
                ->type('#name-input', $this->menuName)
                ->typeSelect2('#type-input', $this->menuTypeFormatted())
                ->waitFor('#url-input')
                ->type('#url-input', $this->menuUrl)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/menus/' . $this->menuLocation . '/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#name-input', $this->menuName)
                ->assertInputValue('#url-input', $this->menuUrl)
                ->assertSee($this->menuTypeFormatted());
        });

        $this->deleteMenu();
    }

    /** @test */
    public function an_admin_can_update_a_menu()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-edit');

        $this->createMenu();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation . '/edit/' . $this->menuModel->id)
                ->type('#name-input', $this->menuNameModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/menus/' . $this->menuLocation)
                ->assertSee('The record was successfully updated!')
                ->waitFor('#root_id_anchor')
                ->click('#root_id_anchor')
                ->waitFor('.js-TreeTable')
                ->assertSee($this->menuNameModified);
        });

        $this->deleteMenuModified();
    }

    /** @test */
    public function an_admin_can_update_a_menu_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-edit');

        $this->createMenu();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation . '/edit/' . $this->menuModel->id)
                ->type('#name-input', $this->menuNameModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/menus/' . $this->menuLocation . '/edit/' . $this->menuModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#name-input', $this->menuNameModified);
        });

        $this->deleteMenuModified();
    }

    /** @test */
    public function an_admin_can_delete_a_menu_if_it_has_permission()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-delete');

        $this->createMenu();

        $this->browse(function ($browser) {
            $browser->resize(1200, 1200)->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/menus/' . $this->menuLocation, $this->menuModel)
                ->waitFor('#root_id_anchor')
                ->click('#root_id_anchor')
                ->waitFor('.js-TreeTable')
                ->assertSee($this->menuName)
                ->clickDeleteRecordButton($this->menuName)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/menus/' . $this->menuLocation, $this->menuModel)
                ->assertDontSee($this->menuName);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_menu_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->revokePermission('menus-delete');

        $this->createMenu();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation)
                ->waitFor('#root_id_anchor')
                ->click('#root_id_anchor')
                ->waitFor('.js-TreeTable')
                ->assertSourceMissing('button-delete');
        });

        $this->deleteMenu();
    }

    /** @test */
    public function it_requires_a_name_when_creating_a_menu()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation . '/create')
                ->typeSelect2('#type-input', $this->menuTypeFormatted())
                ->waitFor('#url-input')
                ->type('#url-input', $this->menuUrl)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });
    }

    /** @test */
    public function it_requires_a_type_when_creating_a_menu()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation . '/create')
                ->type('#name-input', $this->menuName)
                ->press('Save')
                ->waitForText('The type field is required')
                ->assertSee('The type field is required');
        });
    }

    /** @test */
    public function it_requires_a_url_when_creating_a_menu()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation . '/create')
                ->type('#name-input', $this->menuName)
                ->typeSelect2('#type-input', $this->menuTypeFormatted())
                ->waitFor('#url-input')
                ->press('Save')
                ->waitForText('The url field is required')
                ->assertSee('The url field is required');
        });
    }

    /** @test */
    public function it_requires_a_name_when_updating_a_menu()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-edit');

        $this->createMenu();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation . '/edit/' . $this->menuModel->id)
                ->type('#name-input', '')
                ->typeSelect2('#type-input', $this->menuTypeFormatted())
                ->waitFor('#url-input')
                ->type('#url-input', $this->menuUrl)
                ->press('Save')
                ->waitForText('The name field is required')
                ->assertSee('The name field is required');
        });

        $this->deleteMenu();
    }

    /** @test */
    public function it_requires_a_type_when_updating_a_menu()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-edit');

        $this->createMenu();

        $this->browse(function ($browser) {
            $browser->resize(1250, 2500)->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation . '/edit/' . $this->menuModel->id)
                ->type('#name-input', $this->menuName)
                ->click('.select2-selection__clear')
                ->press('Save')
                ->waitForText('The type field is required')
                ->assertSee('The type field is required');
        });

        $this->deleteMenu();
    }

    /** @test */
    public function it_requires_a_url_when_updating_a_menu()
    {
        $this->admin->grantPermission('menus-list');
        $this->admin->grantPermission('menus-edit');

        $this->createMenu();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/menus/' . $this->menuLocation . '/edit/' . $this->menuModel->id)
                ->type('#name-input', $this->menuName)
                ->typeSelect2('#type-input', $this->menuTypeFormatted())
                ->waitFor('#url-input')
                ->type('#url-input', '')
                ->press('Save')
                ->waitForText('The url field is required')
                ->assertSee('The url field is required');
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
