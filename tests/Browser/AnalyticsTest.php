<?php

namespace Varbox\Tests\Browser;

use Varbox\Models\Role;

class AnalyticsTest extends TestCase
{
    /**
     * @var Role
     */
    protected $analyticsModel;

    /** @test */
    public function an_admin_can_view_the_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/analytics')
                ->assertPathIs('/admin/analytics')
                ->assertSee('Analytics Metrics');
        });
    }

    /** @test */
    public function an_admin_can_view_the_page_if_it_has_permission()
    {
        $this->admin->grantPermission('analytics-view');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/analytics')
                ->assertPathIs('/admin/analytics')
                ->assertSee('Analytics Metrics');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('analytics-view');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/analytics')
                ->assertSee('Unauthorized')
                ->assertDontSee('Analytics Metrics');
        });
    }

    /** @test */
    public function an_admin_can_view_the_code_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/analytics')
                ->assertPathIs('/admin/analytics')
                ->assertSee('Analytics Code');
        });
    }

    /** @test */
    public function an_admin_can_view_the_code_if_it_has_permission()
    {
        $this->admin->grantPermission('analytics-view');
        $this->admin->grantPermission('analytics-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/analytics')
                ->assertPathIs('/admin/analytics')
                ->assertSee('Analytics Metrics')
                ->assertSee('Analytics Code');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_code_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('analytics-view');
        $this->admin->revokePermission('analytics-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/analytics')
                ->assertPathIs('/admin/analytics')
                ->assertSee('Analytics Metrics')
                ->assertDontSee('Analytics Code');
        });
    }

    /** @test */
    public function an_admin_can_modify_the_code_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/analytics')
                ->type('#code-input', 'Test Analytics code')
                ->press('Save')
                ->pause(500)
                ->assertSee('The Analytics code was successfully saved!')
                ->assertInputValue('#code-input', 'Test Analytics code');
        });
    }

    /** @test */
    public function an_admin_can_modify_the_code_if_it_has_permission()
    {
        $this->admin->grantPermission('analytics-view');
        $this->admin->grantPermission('analytics-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/analytics')
                ->type('#code-input', 'Test Analytics code')
                ->press('Save')
                ->pause(500)
                ->assertSee('The Analytics code was successfully saved!')
                ->assertInputValue('#code-input', 'Test Analytics code');
        });
    }

    /** @test */
    public function an_admin_doesnt_see_the_metrics_if_the_analytics_account_is_not_setup()
    {
        $this->app['config']->set('varbox.analytics.view_id', null);

        $this->admin->grantPermission('analytics-view');
        $this->admin->grantPermission('analytics-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/analytics')
                ->assertSee('Analytics Metrics')
                ->assertSee('No data to show because the Google Analytics is not configured within the application');
        });
    }
}
