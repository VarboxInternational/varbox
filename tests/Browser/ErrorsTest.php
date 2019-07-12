<?php

namespace Varbox\Tests\Browser;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
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

    /**
     * @var Error
     */
    protected $error1;

    /**
     * @var Error
     */
    protected $error2;

    /**
     * @var Error
     */
    protected $error3;

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

    /** @test */
    public function an_admin_can_delete_an_error_if_it_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createError();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/errors/', $this->errorModel)
                ->assertSee($this->getDisplayedErrorType())
                ->assertSee($this->getDisplayedErrorUrl())
                ->assertSee($this->errorModel->code)
                ->assertSee($this->errorModel->occurrences)
                ->assertSee($this->errorModel->created_at->toDateTimeString())
                ->deleteRecord($this->errorModel->code)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/errors/', $this->errorModel)
                ->assertDontSee($this->getDisplayedErrorType())
                ->assertDontSee($this->getDisplayedErrorUrl())
                ->assertDontSee($this->errorModel->code)
                ->assertDontSee($this->errorModel->occurrences)
                ->assertDontSee($this->errorModel->created_at->toDateTimeString());
        });
    }

    /** @test */
    public function an_admin_can_delete_an_error_if_it_has_permission()
    {
        $this->admin->grantPermission('errors-list');
        $this->admin->grantPermission('errors-delete');

        $this->createError();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visitLastPage('/admin/errors/', $this->errorModel)
                ->assertSee($this->getDisplayedErrorType())
                ->assertSee($this->getDisplayedErrorUrl())
                ->assertSee($this->errorModel->code)
                ->assertSee($this->errorModel->occurrences)
                ->assertSee($this->errorModel->created_at->toDateTimeString())
                ->deleteRecord($this->errorModel->code)
                ->assertSee('The record was successfully deleted!')
                ->visitLastPage('/admin/errors/', $this->errorModel)
                ->assertDontSee($this->getDisplayedErrorType())
                ->assertDontSee($this->getDisplayedErrorUrl())
                ->assertDontSee($this->errorModel->code)
                ->assertDontSee($this->errorModel->occurrences)
                ->assertDontSee($this->errorModel->created_at->toDateTimeString());
        });
    }

    /** @test */
    public function an_admin_cannot_delete_an_error_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('errors-list');
        $this->admin->revokePermission('errors-delete');

        $this->createError();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->deleteAnyRecord()
                ->assertDontSee('The record was successfully deleted!')
                ->assertSee('Unauthorized');
        });

        $this->deleteError();
    }
    
    /** @test */
    public function an_admin_can_delete_old_errors_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createErrors();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->clickButtonWithConfirm('Delete Old Errors')
                ->assertPathIs('/admin/errors')
                ->assertSee('Old errors have been successfully deleted')
                ->assertRecordsCount(2);
        });
    }

    /** @test */
    public function an_admin_can_delete_old_errors_if_it_has_permission()
    {
        $this->admin->grantPermission('errors-list');
        $this->admin->grantPermission('errors-delete');

        $this->createErrors();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->clickButtonWithConfirm('Delete Old Errors')
                ->assertPathIs('/admin/errors')
                ->assertSee('Old errors have been successfully deleted')
                ->assertRecordsCount(2);
        });
    }

    /** @test */
    public function an_admin_cannot_delete_old_errors_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('errors-list');
        $this->admin->revokePermission('errors-delete');

        $this->createErrors();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->clickButtonWithConfirm('Delete Old Errors')
                ->assertDontSee('Old errors have been successfully deleted')
                ->assertSee('Unauthorized')
                ->visit('/admin/errors')
                ->assertRecordsCount(3);
        });

        $this->deleteErrors();
    }

    /** @test */
    public function an_admin_can_delete_all_errors_if_it_is_a_super_admin()
    {
        $this->admin->assignRoles('Super');

        $this->createErrors();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->clickButtonWithConfirm('Delete All Errors')
                ->assertPathIs('/admin/errors')
                ->assertSee('All errors have been successfully deleted')
                ->assertSee('No records found');
        });
    }

    /** @test */
    public function an_admin_can_delete_all_errors_if_it_has_permission()
    {
        $this->admin->grantPermission('errors-list');
        $this->admin->grantPermission('errors-delete');

        $this->createErrors();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->clickButtonWithConfirm('Delete All Errors')
                ->assertPathIs('/admin/errors')
                ->assertSee('All errors have been successfully deleted')
                ->assertSee('No records found');
        });
    }

    /** @test */
    public function an_admin_cannot_delete_all_errors_if_it_doesnt_have_permission()
    {
        $this->admin->grantPermission('errors-list');
        $this->admin->revokePermission('errors-delete');

        $this->createErrors();

        $this->browse(function ($browser) {
            $browser->loginAs($this->admin, 'admin')
                ->visit('/admin/errors')
                ->clickButtonWithConfirm('Delete All Errors')
                ->assertDontSee('All errors have been successfully deleted')
                ->assertSee('Unauthorized')
                ->visit('/admin/errors')
                ->assertRecordsCount(3);
        });

        $this->deleteErrors();
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
    protected function createErrors()
    {
        (new Error)->saveError(new Exception);
        (new Error)->saveError(new AuthenticationException);
        (new Error)->saveError(new ModelNotFoundException);

        $this->error1 = Error::whereType(Exception::class)->first();
        $this->error2 = Error::whereType(AuthenticationException::class)->first();
        $this->error3 = Error::whereType(ModelNotFoundException::class)->first();

        $this->error1->created_at = today()->subDays(31);
        $this->error1->save();
    }

    /**
     * @return void
     */
    protected function deleteError()
    {
        Error::whereType(NotFoundHttpException::class)->first()->delete();
    }

    /**
     * @return void
     */
    protected function deleteErrors()
    {
        Error::whereType(Exception::class)->first()->delete();
        Error::whereType(AuthenticationException::class)->first()->delete();
        Error::whereType(ModelNotFoundException::class)->first()->delete();
    }

    /**
     * @return string
     */
    protected function getDisplayedErrorType()
    {
        return Arr::last(explode('\\', $this->errorModel->type));
    }

    /**
     * @return string
     */
    protected function getDisplayedErrorUrl()
    {
        return str_replace(config('app.url'), '', $this->errorModel->url) ?: '/';
    }
}
