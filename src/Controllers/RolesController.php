<?php

namespace Varbox\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\PermissionModelContract;
use Varbox\Contracts\RoleModelContract;
use Varbox\Contracts\UserModelContract;
use Varbox\Filters\RoleFilter;
use Varbox\Requests\RoleRequest;
use Varbox\Sorts\RoleSort;
use Varbox\Traits\CanCrud;

class RolesController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var RoleModelContract
     */
    protected $model;

    /**
     * @var UserModelContract
     */
    protected $user;

    /**
     * @var PermissionModelContract
     */
    protected $permission;

    /**
     * RolesController constructor.
     *
     * @param RoleModelContract $model
     * @param UserModelContract $user
     * @param PermissionModelContract $permission
     */
    public function __construct(RoleModelContract $model, UserModelContract $user, PermissionModelContract $permission)
    {
        $this->model = $model;
        $this->user = $user;
        $this->permission = $permission;
    }

    /**
     * @param Request $request
     * @param RoleFilter $filter
     * @param RoleSort $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, RoleFilter $filter, RoleSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = $this->model
                ->filtered($request->all(), $filter)
                ->sorted($request->all(), $sort)
                ->paginate(config('varbox.crud.per_page', 10));

            $this->title = 'Roles';
            $this->view = view('varbox::admin.roles.index');
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
            $this->title = 'Add Role';
            $this->view = view('varbox::admin.roles.add');
            $this->vars = [
                'guards' => $this->user->getAllGuards(),
                'adminPermissions' => $this->permission->getGrouped('admin'),
                'webPermissions' => $this->permission->getGrouped('web'),
                'apiPermissions' => $this->permission->getGrouped('api'),
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
        app(config('varbox.bindings.form_requests.role_form_request', RoleRequest::class));

        return $this->_store(function () use ($request) {
            $this->item = $this->model->create($request->all());
            $this->redirect = redirect()->route('admin.roles.index');

            $this->item->permissions()->attach((array)$request->input('permissions'));
        }, $request);
    }

    /**
     * @param RoleModelContract $role
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(RoleModelContract $role)
    {
        return $this->_edit(function () use ($role) {
            $this->item = $role;
            $this->title = 'Edit Role';
            $this->view = view('varbox::admin.roles.edit');
            $this->vars = [
                'guards' => $this->user->getAllGuards(),
                'adminPermissions' => $this->permission->getGrouped('admin'),
                'webPermissions' => $this->permission->getGrouped('web'),
                'apiPermissions' => $this->permission->getGrouped('api'),
            ];
        });
    }

    /**
     * @param Request $request
     * @param RoleModelContract $role
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, RoleModelContract $role)
    {
        app(config('varbox.bindings.form_requests.role_form_request', RoleRequest::class));

        return $this->_update(function () use ($request, $role) {
            $this->item = $role;
            $this->redirect = redirect()->route('admin.roles.index');

            $this->item->update($request->all());
            $this->item->permissions()->sync((array)$request->input('permissions'));
        }, $request);
    }

    /**
     * @param RoleModelContract $role
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(RoleModelContract $role)
    {
        return $this->_destroy(function () use ($role) {
            $this->item = $role;
            $this->redirect = redirect()->route('admin.roles.index');

            $this->item->delete();
        });
    }
}
