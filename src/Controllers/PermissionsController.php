<?php

namespace Varbox\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\PermissionModelContract;
use Varbox\Contracts\UserModelContract;
use Varbox\Filters\PermissionFilter;
use Varbox\Requests\PermissionRequest;
use Varbox\Sorts\PermissionSort;
use Varbox\Traits\CanCrud;

class PermissionsController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var PermissionModelContract
     */
    protected $model;

    /**
     * @var UserModelContract
     */
    protected $user;

    /**
     * PermissionsController constructor.
     *
     * @param PermissionModelContract $model
     * @param UserModelContract $user
     */
    public function __construct(PermissionModelContract $model, UserModelContract $user)
    {
        $this->model = $model;
        $this->user = $user;
    }

    /**
     * @param Request $request
     * @param PermissionFilter $filter
     * @param PermissionSort $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, PermissionFilter $filter, PermissionSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = $this->model
                ->filtered($request->all(), $filter)
                ->sorted($request->all(), $sort)
                ->paginate(config('varbox.varbox-crud.per_page', 10));

            $this->title = 'Permissions';
            $this->view = view('varbox::admin.auth.permissions.index');
            $this->vars = [
                'guards' => $this->user->getAllGuards(),
            ];
        });
    }

    /**
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Permission';
            $this->view = view('varbox::admin.auth.permissions.add');
            $this->vars = [
                'guards' => $this->user->getAllGuards(),
            ];
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        app(config('varbox.varbox-binding.form_requests.permission_form_request', PermissionRequest::class));

        return $this->_store(function () use ($request) {
            $this->item = $this->model->create($request->all());
            $this->redirect = redirect()->route('admin.permissions.index');
        }, $request);
    }

    /**
     * @param PermissionModelContract $permission
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(PermissionModelContract $permission)
    {
        return $this->_edit(function () use ($permission) {
            $this->item = $permission;
            $this->title = 'Edit Permission';
            $this->view = view('varbox::admin.auth.permissions.edit');
            $this->vars = [
                'guards' => $this->user->getAllGuards(),
            ];
        });
    }

    /**
     * @param Request $request
     * @param PermissionModelContract $permission
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, PermissionModelContract $permission)
    {
        app(config('varbox.varbox-binding.form_requests.permission_form_request', PermissionRequest::class));

        return $this->_update(function () use ($request, $permission) {
            $this->item = $permission;
            $this->redirect = redirect()->route('admin.permissions.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param PermissionModelContract $permission
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(PermissionModelContract $permission)
    {
        return $this->_destroy(function () use ($permission) {
            $this->item = $permission;
            $this->redirect = redirect()->route('admin.permissions.index');

            $this->item->delete();
        });
    }
}
