<?php

namespace Varbox\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\LanguageModelContract;
use Varbox\Filters\RoleFilter;
use Varbox\Requests\LanguageRequest;
use Varbox\Sorts\RoleSort;
use Varbox\Traits\CanCrud;

class LanguagesController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var LanguageModelContract
     */
    protected $model;

    /**
     * RolesController constructor.
     *
     * @param LanguageModelContract $model
     */
    public function __construct(LanguageModelContract $model)
    {
        $this->model = $model;
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

            $this->title = 'Languages';
            $this->view = view('varbox::admin.languages.index');
        });
    }

    /**
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Language';
            $this->view = view('varbox::admin.languages.add');
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        app(config('varbox.bindings.form_requests.language_form_request', LanguageRequest::class));

        return $this->_store(function () use ($request) {
            $this->item = $this->model->create($request->all());
            $this->redirect = redirect()->route('admin.languages.index');
        }, $request);
    }

    /**
     * @param LanguageModelContract $language
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(LanguageModelContract $language)
    {
        return $this->_edit(function () use ($language) {
            $this->item = $language;
            $this->title = 'Edit Language';
            $this->view = view('varbox::admin.languages.edit');
        });
    }

    /**
     * @param Request $request
     * @param LanguageModelContract $language
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, LanguageModelContract $language)
    {
        app(config('varbox.bindings.form_requests.language_form_request', LanguageRequest::class));

        return $this->_update(function () use ($request, $language) {
            $this->item = $language;
            $this->redirect = redirect()->route('admin.languages.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param LanguageModelContract $language
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(LanguageModelContract $language)
    {
        return $this->_destroy(function () use ($language) {
            $this->item = $language;
            $this->redirect = redirect()->route('admin.languages.index');

            $this->item->delete();
        });
    }

    /**
     * @param LanguageModelContract $language
     * @return \Illuminate\Http\RedirectResponse
     */
    public function change(LanguageModelContract $language)
    {
        session()->put('locale', $language->code);

        return back();
    }
}
