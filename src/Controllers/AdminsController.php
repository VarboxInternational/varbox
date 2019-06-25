<?php

namespace Varbox\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\RoleModelContract;
use Varbox\Contracts\UserModelContract;
use Varbox\Filters\AdminFilter;
use Varbox\Requests\AdminRequest;
use Varbox\Sorts\AdminSort;
use Varbox\Traits\CanCrud;

class AdminsController extends Controller
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
     * AdminsController constructor.
     *
     * @param UserModelContract $model
     * @param RoleModelContract $user
     */
    public function __construct(UserModelContract $model, RoleModelContract $role)
    {
        $this->model = $model;
        $this->role = $role;
    }

    /**
     * @param Request $request
     * @param AdminFilter $filter
     * @param AdminSort $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, AdminFilter $filter, AdminSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = $this->model->onlyAdmins()
                ->filtered($request->all(), $filter)
                ->sorted($request->all(), $sort)
                ->paginate(config('varbox.varbox-crud.per_page', 10));

            $this->title = 'Admins';
            $this->view = view('varbox::admin.admins.index');
            $this->vars = [
                'roles' => $this->role->whereGuard('admin')->get(),
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
            $this->title = 'Add Admin';
            $this->view = view('varbox::admin.admins.add');
            $this->vars = [
                'roles' => $this->role->whereGuard('admin')->get(),
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
            $this->item = $this->model->doNotLogActivity()->create($request->all());
            $this->redirect = redirect()->route('admin.admins.index');

            $this->item->roles()->attach($request->input('roles'));
            $this->item->logActivity('created');
        }, $request);
    }

    /**
     * @param UserModelContract $user
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(UserModelContract $user)
    {
        $this->guardAgainstNonAdmin($user);

        return $this->_edit(function () use ($user) {
            $this->item = $user;
            $this->title = 'Edit Admin';
            $this->view = view('varbox::admin.admins.edit');
            $this->vars = [
                'roles' => $this->role->whereGuard('admin')->get(),
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
        $this->guardAgainstNonAdmin($user);

        $request = $this->initRequest();

        return $this->_update(function () use ($request, $user) {
            $this->item = $user;
            $this->redirect = redirect()->route('admin.admins.index');

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
        $this->guardAgainstNonAdmin($user);

        if (auth()->id() ===$user->id) {
            flash()->error('You cannot delete yourself!');
            return back();
        }

        return $this->_destroy(function () use ($user) {
            $this->item = $user;
            $this->redirect = redirect()->route('admin.admins.index');

            $this->item->delete();
        });
    }

    /**
     * @return mixed
     */
    protected function initRequest()
    {
        return app(config(
            'varbox.varbox-binding.form_requests.admin_form_request', AdminRequest::class
        ))->merged();
    }

    /**
     * @param UserModelContract $user
     * @return void
     */
    protected function guardAgainstNonAdmin(UserModelContract $user)
    {
        if (!$user->isAdmin()) {
            abort(404);
        }
    }
}
