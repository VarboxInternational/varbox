<?php

namespace Varbox\Tests\Browser;

use Carbon\Carbon;
use Varbox\Models\Redirect;

class RedirectsTest extends TestCase
{
    /**
     * @var Redirect
     */
    protected $redirectModel;

    /**
     * @var string
     */
    protected $redirectOldUrl = 'test/old-url';

    /**
     * @var string
     */
    protected $redirectNewUrl = 'test/new-url';

    /**
     * @var string
     */
    protected $redirectNewUrlModified = 'test/new-url/modified';

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->assertPathIs('/admin/redirects')
                ->assertSee('Redirects');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('redirects-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->assertPathIs('/admin/redirects')
                ->assertSee('Redirects');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('redirects-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->assertSee('Unauthorized')
                ->assertDontSee('Redirects');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->clickLink('Add New')
                ->assertPathIs('/admin/redirects/create')
                ->assertSee('Add Redirect');
        });
    }

    /** @test */
    public function an_admin_can_view_the_add_page_if_it_has_permission()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->clickLink('Add New')
                ->assertPathIs('/admin/redirects/create')
                ->assertSee('Add Redirect');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_add_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->revokePermission('redirects-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->assertDontSee('Add New')
                ->visit('/admin/redirects/create')
                ->assertSee('Unauthorized')
                ->assertDontSee('Add Redirect');
        });
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createRedirect();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/redirects', $this->redirectModel)
                ->clickEditRecordButton($this->redirectNewUrl)
                ->assertPathIs('/admin/redirects/edit/' . $this->redirectModel->id)
                ->assertSee('Edit Redirect');
        });

        $this->deleteRedirect();
    }

    /** @test */
    public function an_admin_can_view_the_edit_page_if_it_has_permission()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-edit');

        $this->createRedirect();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/redirects', $this->redirectModel)
                ->clickEditRecordButton($this->redirectNewUrl)
                ->assertPathIs('/admin/redirects/edit/' . $this->redirectModel->id)
                ->assertSee('Edit Redirect');
        });

        $this->deleteRedirect();
    }

    /** @test */
    public function an_admin_cannot_view_the_edit_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->revokePermission('redirects-edit');

        $this->createRedirect();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/redirects', $this->redirectModel)
                ->assertSourceMissing('button-edit')
                ->visit('/admin/redirects/edit/' . $this->redirectModel->id)
                ->assertSee('Unauthorized')
                ->assertDontSee('Edit Redirect');
        });

        $this->deleteRedirect();
    }

    /** @test */
    public function an_admin_can_create_a_redirect()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->clickLink('Add New')
                ->type('#old_url-input', $this->redirectOldUrl)
                ->type('#new_url-input', $this->redirectNewUrl)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/redirects')
                ->assertSee('The record was successfully created!')
                ->visitLastPage('/admin/redirects/', new Redirect)
                ->assertSee($this->redirectOldUrl)
                ->assertSee($this->redirectNewUrl);
        });

        $this->deleteRedirect();
    }

    /** @test */
    public function an_admin_can_create_a_redirect_and_stay_to_create_another_one()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->clickLink('Add New')
                ->type('#old_url-input', $this->redirectOldUrl)
                ->type('#new_url-input', $this->redirectNewUrl)
                ->clickLink('Save & New')
                ->pause(500)
                ->assertPathIs('/admin/redirects/create')
                ->assertSee('The record was successfully created!');
        });

        $this->deleteRedirect();
    }

    /** @test */
    public function an_admin_can_create_a_redirect_and_continue_editing_it()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-add');
        $this->admin->grantPermission('redirects-edit');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->clickLink('Add New')
                ->type('#old_url-input', $this->redirectOldUrl)
                ->type('#new_url-input', $this->redirectNewUrl)
                ->clickLink('Save & Continue')
                ->pause(500)
                ->assertPathBeginsWith('/admin/redirects/edit')
                ->assertSee('The record was successfully created!')
                ->assertInputValue('#old_url-input', $this->redirectOldUrl)
                ->assertInputValue('#new_url-input', $this->redirectNewUrl);
        });

        $this->deleteRedirect();
    }

    /** @test */
    public function an_admin_can_update_a_redirect()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-edit');

        $this->createRedirect();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/redirects', $this->redirectModel)
                ->clickEditRecordButton($this->redirectNewUrl)
                ->type('#new_url-input', $this->redirectNewUrlModified)
                ->press('Save')
                ->pause(500)
                ->assertPathIs('/admin/redirects')
                ->assertSee('The record was successfully updated!')
                ->visitLastPage('admin/redirects', $this->redirectModel)
                ->assertSee($this->redirectNewUrlModified);
        });

        $this->deleteRedirectModified();
    }

    /** @test */
    public function an_admin_can_update_a_redirect_and_stay_to_continue_editing_id()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-edit');

        $this->createRedirect();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/redirects', $this->redirectModel)
                ->clickEditRecordButton($this->redirectNewUrl)
                ->type('#new_url-input', $this->redirectNewUrlModified)
                ->clickLink('Save & Stay')
                ->pause(500)
                ->assertPathIs('/admin/redirects/edit/' . $this->redirectModel->id)
                ->assertSee('The record was successfully updated!')
                ->assertInputValue('#new_url-input', $this->redirectNewUrlModified);
        });

        $this->deleteRedirectModified();
    }

    /** @test */
    public function an_admin_can_delete_a_redirect_if_it_has_permission()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-delete');

        $this->createRedirect();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/redirects/', $this->redirectModel)
                ->assertSee($this->redirectOldUrl)
                ->clickDeleteRecordButton($this->redirectOldUrl)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/redirects/', $this->redirectModel)
                ->assertDontSee($this->redirectOldUrl);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_a_redirect_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->revokePermission('redirects-delete');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->assertSourceMissing('button-delete');
        });
    }

    /** @test */
    public function an_admin_can_export_redirects_if_it_has_permission()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-export');

        $this->createRedirect();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->clickButtonWithConfirm('Export File')
                ->assertSee('All redirects have been successfully exported to the "bootstrap/redirects.php" file!');
        });

        $this->assertFileExists(base_path('bootstrap/redirects.php'));

        $this->deleteRedirect();
    }

    /** @test */
    public function an_admin_cannot_export_redirects_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->revokePermission('redirects-export');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->assertDontSee('Export File');
        });
    }

    /** @test */
    public function an_admin_can_delete_all_redirects_if_it_has_permission()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-delete');

        Redirect::create([
            'old_url' => 'old-url-1',
            'new_url' => 'new-url-1',
        ]);

        Redirect::create([
            'old_url' => 'old-url-2',
            'new_url' => 'new-url-2',
        ]);

        Redirect::create([
            'old_url' => 'old-url-3',
            'new_url' => 'new-url-3',
        ]);

        $this->assertEquals(3, Redirect::count());

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->assertDontSee('No records found')
                ->clickButtonWithConfirm('Delete All')
                ->assertSee('All redirects have been successfully deleted!')
                ->visit('/admin/redirects/')
                ->assertSee('No records found');
        });

        $this->assertEquals(0, Redirect::count());
    }

    /** @test */
    public function an_admin_cannot_delete_all_redirects_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->revokePermission('redirects-delete');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->assertDontSee('Delete All');
        });
    }

    /** @test */
    public function an_admin_can_filter_redirects_by_keyword()
    {
        $this->admin->grantPermission('redirects-list');

        $this->createRedirect();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->filterRecordsByText('#search-input', $this->redirectOldUrl)
                ->assertQueryStringHas('search', $this->redirectOldUrl)
                ->assertSee($this->redirectOldUrl)
                ->assertRecordsCount(1)
                ->assertDontSee('No records found')
                ->visit('/admin/redirects')
                ->filterRecordsByText('#search-input', $this->redirectNewUrl)
                ->assertQueryStringHas('search', $this->redirectNewUrl)
                ->assertSee($this->redirectNewUrl)
                ->assertRecordsCount(1)
                ->assertDontSee('No records found');
        });

        $this->deleteRedirect();
    }

    /** @test */
    public function an_admin_can_filter_redirects_by_status()
    {
        $this->admin->grantPermission('redirects-list');

        $this->createRedirect();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->filterRecordsBySelect('#status-input', '301')
                ->assertQueryStringHas('status', '301')
                ->assertRecordsCount(1)
                ->assertSee($this->redirectNewUrl)
                ->visit('/admin/redirects')
                ->filterRecordsBySelect('#status-input', '302')
                ->assertQueryStringHas('status', '302')
                ->assertSee('No records found');
        });

        $this->deleteRedirect();
    }

    /** @test */
    public function an_admin_can_filter_redirects_by_start_date()
    {
        $this->admin->grantPermission('redirects-list');

        $this->createRedirect();

        $past = Carbon::now()->subDays(7)->format('Y-m-d');
        $future = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->filterRecordsByText('#start_date-input', $past)
                ->assertQueryStringHas('start_date', urlencode($past))
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/redirects', $this->redirectModel)
                ->assertSee($this->redirectNewUrl)
                ->visit('/admin/redirects')
                ->filterRecordsByText('#start_date-input', $future)
                ->assertQueryStringHas('start_date', urlencode($future))
                ->assertSee('No records found');
        });

        $this->deleteRedirect();
    }

    /** @test */
    public function an_admin_can_filter_redirects_by_end_date()
    {
        $this->admin->grantPermission('redirects-list');

        $this->createRedirect();

        $past = Carbon::now()->subDays(7)->format('Y-m-d');
        $future = Carbon::now()->addDays(7)->format('Y-m-d');

        $this->browse(function ($browser) use ($past, $future) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->filterRecordsByText('#end_date-input', $past)
                ->assertQueryStringHas('end_date', urlencode($past))
                ->assertSee('No records found')
                ->visit('/admin/redirects')
                ->filterRecordsByText('#end_date-input', $future)
                ->assertQueryStringHas('end_date', urlencode($future))
                ->assertDontSee('No records found')
                ->visitLastPage('/admin/redirects', $this->redirectModel)
                ->assertSee($this->redirectNewUrl);
        });

        $this->deleteRedirect();
    }

    /** @test */
    public function an_admin_can_clear_redirect_filters()
    {
        $this->admin->grantPermission('redirects-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects/?search=list&status=301&start_date=1970-01-01&end_date=2070-01-01')
                ->assertQueryStringHas('search')
                ->assertQueryStringHas('status')
                ->assertQueryStringHas('start_date')
                ->assertQueryStringHas('end_date')
                ->clickLink('Clear')
                ->assertPathIs('/admin/redirects/')
                ->assertQueryStringMissing('search')
                ->assertQueryStringMissing('status')
                ->assertQueryStringMissing('start_date')
                ->assertQueryStringMissing('end_date');
        });
    }

    /** @test */
    public function it_requires_an_old_url_when_creating_a_redirect()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->clickLink('Add New')
                ->type('#new_url-input', $this->redirectNewUrl)
                ->press('Save')
                ->waitForText('The old url field is required')
                ->assertSee('The old url field is required');
        });
    }

    /** @test */
    public function it_requires_a_unique_old_url_when_creating_a_redirect()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-add');

        $this->createRedirect();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->clickLink('Add New')
                ->type('#old_url-input', $this->redirectOldUrl)
                ->type('#new_url-input', $this->redirectNewUrl)
                ->press('Save')
                ->waitForText('The old url has already been taken')
                ->assertSee('The old url has already been taken');
        });

        $this->deleteRedirect();
    }

    /** @test */
    public function it_requires_a_new_url_when_creating_a_redirect()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-add');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->clickLink('Add New')
                ->type('#old_url-input', $this->redirectOldUrl)
                ->press('Save')
                ->waitForText('The new url field is required')
                ->assertSee('The new url field is required');
        });
    }

    /** @test */
    public function it_requires_an_old_url_when_updating_a_redirect()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-edit');

        $this->createRedirect();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->click('.button-edit')
                ->type('#old_url-input', '')
                ->type('#new_url-input', $this->redirectNewUrl)
                ->press('Save')
                ->waitForText('The old url field is required')
                ->assertSee('The old url field is required');
        });

        $this->deleteRedirect();
    }

    /** @test */
    public function it_requires_a_new_url_when_updating_a_redirect()
    {
        $this->admin->grantPermission('redirects-list');
        $this->admin->grantPermission('redirects-edit');

        $this->createRedirect();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/redirects')
                ->click('.button-edit')
                ->type('#old_url-input', $this->redirectOldUrl)
                ->type('#new_url-input', '')
                ->press('Save')
                ->waitForText('The new url field is required')
                ->assertSee('The new url field is required');
        });

        $this->deleteRedirect();
    }

    /**
     * @return void
     */
    protected function createRedirect()
    {
        $this->redirectModel = Redirect::create([
            'old_url' => $this->redirectOldUrl,
            'new_url' => $this->redirectNewUrl,
        ]);
    }

    /**
     * @return void
     */
    protected function deleteRedirect()
    {
        Redirect::whereNewUrl($this->redirectNewUrl)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteRedirectModified()
    {
        Redirect::whereNewUrl($this->redirectNewUrlModified)->first()->delete();
    }
}
