<?php

namespace Varbox\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\RoleModelContract;
use Varbox\Contracts\UserModelContract;
use Varbox\Filters\UserFilter;
use Varbox\Requests\UserRequest;
use Varbox\Sorts\UserSort;
use Varbox\Traits\CanCrud;

class UsersController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var UserModelContract
     */
    protected $model;

    /**
     * @var RoleModelContract
     */
    protected $role;

    /**
     * UsersController constructor.
     *
     * @param UserModelContract $model
     * @param RoleModelContract $role
     */
    public function __construct(UserModelContract $model, RoleModelContract $role)
    {
        $this->model = $model;
        $this->role = $role;
    }

    /**
     * @param Request $request
     * @param UserFilter $filter
     * @param UserSort $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, UserFilter $filter, UserSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = $this->model->excludingAdmins()
                ->filtered($request->all(), $filter)
                ->sorted($request->all(), $sort)
                ->paginate(config('varbox.varbox-crud.per_page', 10));

            $this->title = 'Users';
            $this->view = view('varbox::admin.users.index');
            $this->vars = [
                'roles' => $this->role->whereGuard('web')->get(),
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
            $this->title = 'Add User';
            $this->view = view('varbox::admin.users.add');
            $this->vars = [
                'roles' => $this->role->whereGuard('web')->get(),
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
        $request = $this->initRequest();

        return $this->_store(function () use ($request) {
            $this->item = $this->model->create($request->all());
            $this->redirect = redirect()->route('admin.users.index');

            $this->item->roles()->attach($request->input('roles'));
        }, $request);
    }

    /**
     * @param UserModelContract $user
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(UserModelContract $user)
    {
        return $this->_edit(function () use ($user) {
            $this->item = $user;
            $this->title = 'Edit User';
            $this->view = view('varbox::admin.users.edit');
            $this->vars = [
                'roles' => $this->role->whereGuard('web')->get(),
            ];
        });
    }

    /**
     * @param Request $request
     * @param UserModelContract $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, UserModelContract $user)
    {
        $request = $this->initRequest();

        return $this->_update(function () use ($request, $user) {
            $this->item = $user;
            $this->redirect = redirect()->route('admin.users.index');

            $this->item->update($request->all());
            $this->item->roles()->sync($request->input('roles'));
        }, $request);
    }

    /**
     * @param UserModelContract $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(UserModelContract $user)
    {
        return $this->_destroy(function () use ($user) {
            $this->item = $user;
            $this->redirect = redirect()->route('admin.users.index');

            $this->item->delete();
        });
    }

    /**
     * @param UserModelContract $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function impersonate(UserModelContract $user)
    {
        auth()->guard('web')->login($user);
        flash()->success('You are now signed in as ' . $user->full_name);

        return redirect('/');
    }

    /**
     * @return mixed
     */
    protected function initRequest()
    {
        return app(config(
            'varbox.varbox-binding.form_requests.user_form_request', UserRequest::class
        ))->merged();
    }
}
