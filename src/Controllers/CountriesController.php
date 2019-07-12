<?php

namespace Varbox\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\CountryModelContract;
use Varbox\Filters\CountryFilter;
use Varbox\Requests\CountryRequest;
use Varbox\Sorts\CountrySort;
use Varbox\Traits\CanCrud;

class CountriesController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var CountryModelContract
     */
    protected $model;

    /**
     * @param CountryModelContract $countryModel
     */
    public function __construct(CountryModelContract $countryModel)
    {
        $this->model = $countryModel;
    }

    /**
     * @param Request $request
     * @param CountryFilter $filter
     * @param CountrySort $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, CountryFilter $filter, CountrySort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $this->items = $this->model
                ->filtered($request->all(), $filter)
                ->sorted($request->all(), $sort)
                ->paginate(config('varbox.crud.per_page', 10));

            $this->title = 'Countries';
            $this->view = view('varbox::admin.countries.index');
        });
    }

    /**
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function create()
    {
        return $this->_create(function () {
            $this->title = 'Add Country';
            $this->view = view('varbox::admin.countries.add');
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request)
    {
        app(config('varbox.bindings.form_requests.country_form_request', CountryRequest::class));

        return $this->_store(function () use ($request) {
            $this->item = $this->model->create($request->all());
            $this->redirect = redirect()->route('admin.countries.index');
        }, $request);
    }

    /**
     * @param CountryModelContract $country
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(CountryModelContract $country)
    {
        return $this->_edit(function () use ($country) {
            $this->item = $country;
            $this->title = 'Edit Country';
            $this->view = view('varbox::admin.countries.edit');
        });
    }

    /**
     * @param Request $request
     * @param CountryModelContract $country
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, CountryModelContract $country)
    {
        app(config('varbox.bindings.form_requests.country_form_request', CountryRequest::class));

        return $this->_update(function () use ($request, $country) {
            $this->item = $country;
            $this->redirect = redirect()->route('admin.countries.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param CountryModelContract $country
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(CountryModelContract $country)
    {
        return $this->_destroy(function () use ($country) {
            $this->item = $country;
            $this->redirect = redirect()->route('admin.countries.index');

            $this->item->delete();
        });
    }
}
