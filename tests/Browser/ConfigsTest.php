<?php

namespace Varbox\Tests\Browser;

use Varbox\Models\Config;

class ConfigsTest extends TestCase
{
    /**
     * @var Config
     */
    protected $configModel;

    /**
     * @var string
     */
    protected $configKey = 'app.name';
    protected $configValue = 'Test App Name';

    /**
     * @var string
     */
    protected $configValueModified = 'Test App Name Modified';

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/configs')
                ->assertPathIs('/admin/configs')
                ->assertSee('Configs');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('configs-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/configs')
                ->assertPathIs('/admin/configs')
                ->assertSee('Configs');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('configs-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/configs')
                ->assertSee('Unauthorized')
                ->assertDontSee('Configs');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/configs')
                ->clickLink('Add New')
                ->assertPathIs('/admin/configs/create')
                ->assertSee('Add Config');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('configs-list');
        $this->admin->grantPermission('configs-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/configs')
                ->clickLink('Add New')
                ->assertPathIs('/admin/configs/create')
                ->assertSee('Add Config');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('configs-list');
        $this->admin->revokePermission('configs-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/configs')
                ->clickLink('Add New')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Config');
        });
    }

    /**
     * @return void
     */
    protected function createConfig()
    {
        $this->configModel = Config::create([
            'key' => $this->configKey,
            'code' => $this->configValue,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteConfig()
    {
        Config::whereValue($this->configValue)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteCountryModified()
    {
        Config::whereValue($this->configValueModified)->first()->delete();
    }
}
