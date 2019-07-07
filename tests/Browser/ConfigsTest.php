<?php

namespace Varbox\Tests\Browser;

use Illuminate\Contracts\Foundation\Application;
use Varbox\Models\Config;
use Varbox\Models\User;

class ConfigsTest extends TestCase
{
    /**
     * @var Config
     */
    protected $configModel;

    /**
     * @var array
     */
    protected $configKeys;

    /**
     * @var string
     */
    protected $configKey = 'app.name';
    protected $configValue = 'Test App Name';

    /**
     * @var string
     */
    protected $configValueModified = 'Test App Name Modified';

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->afterApplicationCreated(function () {
            $this->configKeys = Config::getAllowedKeys();
        });
    }

    /**
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('varbox.varbox-config.keys', ['app.name']);
    }

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

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createConfig();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/configs', $this->configModel)
                ->clickEditButton($this->configKeys[$this->configKey])
                ->assertPathIs('/admin/configs/edit/' . $this->configModel->id)
                ->assertSee('Edit Config');
        });

        $this->deleteConfig();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('configs-list');
        $this->admin->grantPermission('configs-edit');

        $this->createConfig();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/configs', $this->configModel)
                ->clickEditButton($this->configKeys[$this->configKey])
                ->assertPathIs('/admin/configs/edit/' . $this->configModel->id)
                ->assertSee('Edit Config');
        });

        $this->deleteConfig();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('configs-list');
        $this->admin->revokePermission('configs-edit');

        $this->createConfig();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/configs', $this->configModel)
                ->clickEditButton($this->configKeys[$this->configKey])
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Config');
        });

        $this->deleteConfig();
    }

    /** @test */
    public function an_admin_can_create_a_config()
    {
        $this->admin->grantPermission('configs-list');
        $this->admin->grantPermission('configs-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/configs')
                ->clickLink('Add New')
                ->select2('#key-input', $this->configKeys[$this->configKey])
                ->type('#value-input', $this->configValue)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/configs')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/configs/', new Config)
                ->assertSee($this->configKeys[$this->configKey])
                ->assertSee($this->configValue);
        });

        $this->deleteConfig();
    }

    /** @test */
    public function an_admin_can_create_a_config_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('configs-list');
        $this->admin->grantPermission('configs-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/configs')
                ->clickLink('Add New')
                ->select2('#key-input', $this->configKeys[$this->configKey])
                ->type('#value-input', $this->configValue)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/configs/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deleteConfig();
    }

    /** @test */
    public function an_admin_can_create_a_config_and_continue_editing_it()
    {
        $this->admin->grantPermission('configs-list');
        $this->admin->grantPermission('configs-add');
        $this->admin->grantPermission('configs-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/configs')
                ->clickLink('Add New')
                ->select2('#key-input', $this->configKeys[$this->configKey])
                ->type('#value-input', $this->configValue)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/configs/edit')
                ->assertSee('The record was successfully created!')
                ->assertSee($this->configKeys[$this->configKey])
                ->assertInputValue('#value-input', $this->configValue);
        });

        $this->deleteConfig();
    }

    /** @test */
    public function an_admin_can_update_a_config()
    {
        $this->admin->grantPermission('configs-list');
        $this->admin->grantPermission('configs-edit');

        $this->createConfig();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/configs', $this->configModel)
                ->clickEditButton($this->configKeys[$this->configKey])
                ->type('#value-input', $this->configValueModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/configs')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/configs', $this->configModel)
                ->assertSee($this->configValueModified);
        });

        $this->deleteConfig();
    }

    /** @test */
    public function an_admin_can_update_a_config_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('configs-list');
        $this->admin->grantPermission('configs-edit');

        $this->createConfig();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/configs', $this->configModel)
                ->clickEditButton($this->configKeys[$this->configKey])
                ->type('#value-input', $this->configValueModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/configs/edit/' . $this->configModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#value-input', $this->configValueModified);
        });

        $this->deleteConfig();
    }

    /** @test */
    public function an_admin_can_delete_a_config_if_it_has_permission()
    {
        $this->admin->grantPermission('configs-list');
        $this->admin->grantPermission('configs-delete');

        $this->createConfig();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/configs/', $this->configModel)
                ->assertSee($this->configKeys[$this->configKey])
                ->assertSee($this->configValue)
                ->deleteRecord($this->configKeys[$this->configKey])
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/configs/', $this->configModel)
                ->assertDontSee($this->configKeys[$this->configKey])
                ->assertDontSee($this->configValue);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_config_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('configs-list');
        $this->admin->revokePermission('configs-delete');

        $this->createConfig();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/configs')
                ->deleteAnyRecord()
                ->assertDontSee('The record was successfully deleted!')
                ->assertSee('Unauthorized');
        });

        $this->deleteConfig();
    }

    /**
     * @return void
     */
    protected function createConfig()
    {
        $this->configModel = Config::create([
            'key' => $this->configKey,
            'value' => $this->configValue,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteConfig()
    {
        Config::where('key', $this->configKey)->first()->delete();
    }
}
