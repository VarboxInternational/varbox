<?php

namespace Varbox\Tests\Browser;

use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Varbox\Models\Activity;
use Varbox\Models\Error;

class ErrorsTest extends TestCase
{
    /**
     * @var Error
     */
    protected $errorModel;

    /**
     * @var Exception
     */
    protected $errorException;

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->assertPathIs('/admin/errors')
                ->assertSee('Errors');
        });
    }

    /** @test */
    public function an_admin_can_view_the_list_page_if_it_has_permission()
    {
        $this->admin->grantPermission('errors-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->assertPathIs('/admin/errors')
                ->assertSee('Errors');
        });
    }

    /** @test */
    public function an_admin_cannot_view_the_list_page_if_it_doesnt_have_permission()
    {
        $this->admin->revokePermission('errors-list');

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->assertSee('Unauthorized')
                ->assertDontSee('Errors');
        });
    }

    /** @test */
    public function an_admin_can_view_the_show_page_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createError();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/errors', $this->errorModel)
                ->clickViewButton($this->errorModel->code)
                ->assertPathIs('/admin/errors/show/' . $this->errorModel->id)
                ->assertSee('View Error');
        });

        $this->deleteError();
    }

    /** @test */
    public function an_admin_can_view_the_show_page_if_it_has_permission()
    {
        $this->admin->grantPermission('errors-list');
        $this->admin->grantPermission('errors-view');

        $this->createError();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/errors', $this->errorModel)
                ->clickViewButton($this->errorModel->code)
                ->assertPathIs('/admin/errors/show/' . $this->errorModel->id)
                ->assertSee('View Error');
        });

        $this->deleteError();
    }

    /** @test */
    public function an_admin_cannot_view_the_show_page_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('errors-list');
        $this->admin->revokePermission('errors-view');

        $this->createError();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/errors', $this->errorModel)
                ->clickViewButton($this->errorModel->code)
                ->assertPathIs('/admin/errors/show/' . $this->errorModel->id)
                ->assertSee('Unauthorized')
                ->assertDontSee('Errors');
        });

        $this->deleteError();
    }

    /**
     * @return void
     */
    protected function createError()
    {
        $this->errorException = new NotFoundHttpException('Page not found', null, 404);

        (new Error)->saveError($this->errorException);

        $this->errorModel = Error::first();
    }

    /**
     * @return void
     */
    protected function deleteError()
    {
        Error::whereType(NotFoundHttpException::class)->first()->delete();
    }
}
