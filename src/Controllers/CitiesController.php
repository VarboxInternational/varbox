<?php

namespace Varbox\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\CityFilterContract;
use Varbox\Contracts\CityModelContract;
use Varbox\Contracts\CitySortContract;
use Varbox\Contracts\CountryModelContract;
use Varbox\Contracts\StateModelContract;
use Varbox\Requests\CityRequest;
use Varbox\Traits\CanCrud;

class CitiesController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var CityModelContract
     */
    protected $model;

    /**
     * @var CountryModelContract
     */
    protected $country;

    /**
     * @var StateModelContract
     */
    protected $state;

    /**
     * @param CityModelContract $model
     * @param CountryModelContract $country
     * @param StateModelContract $state
     */
    public function __construct(CityModelContract $model, CountryModelContract $country, StateModelContract $state)
    {
        $this->model = $model;
        $this->country = $country;
        $this->state = $state;
    }

    /**
     * @param Request $request
     * @param CityFilterContract $filter
     * @param CitySortContract $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, CityFilterContract $filter, CitySortContract $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $query = $this->model->with([
                'country', 'state'
            ])->filtered($request->all(), $filter);

            if ($request->filled('sort')) {
                $query->sorted($request->all(), $sort);
            } else {
                $query->alphabetically();
            }

            $this->items = $query->paginate(config('varbox.crud.per_page', 30));
            $this->title = 'Cities';
            $this->view = view('varbox::admin.cities.index');
            $this->vars = [
                'countries' => $this->country->alphabetically()->get(),
                'states' => $this->state->alphabetically()->get(),
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
            $this->title = 'Add City';
            $this->view = view('varbox::admin.cities.add');
            $this->vars = [
                'countries' => $this->country->alphabetically()->get(),
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
        app(config('varbox.bindings.form_requests.city_form_request', CityRequest::class));

        return $this->_store(function () use ($request) {
            $this->item = $this->model->create($request->all());
            $this->redirect = redirect()->route('admin.cities.index');
        }, $request);
    }

    /**
     * @param CityModelContract $city
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(CityModelContract $city)
    {
        return $this->_edit(function () use ($city) {
            $this->item = $city;
            $this->title = 'Edit City';
            $this->view = view('varbox::admin.cities.edit');
            $this->vars = [
                'countries' => $this->country->alphabetically()->get(),
                'states' => $this->state->fromCountry($city->country_id)->get(),
            ];
        });
    }

    /**
     * @param Request $request
     * @param CityModelContract $city
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, CityModelContract $city)
    {
        app(config('varbox.bindings.form_requests.city_form_request', CityRequest::class));

        return $this->_update(function () use ($request, $city) {
            $this->item = $city;
            $this->redirect = redirect()->route('admin.cities.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param CityModelContract $city
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(CityModelContract $city)
    {
        return $this->_destroy(function () use ($city) {
            $this->item = $city;
            $this->redirect = redirect()->route('admin.cities.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param CountryModelContract|null $country
     * @param StateModelContract|null $state
     * @return array
     */
    public function get(Request $request, CountryModelContract $country = null, StateModelContract $state = null)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $query = $this->model->alphabetically();

        if ($country && $country->exists) {
            $query->fromCountry($country->getKey());
        }

        if ($state && $state->exists) {
            $query->fromState($state->getKey());
        }

        $cities = [];

        foreach ($query->get() as $index => $city) {
            $cities[] = [
                'id' => $city->getKey(),
                'name' => $city->name,
            ];
        }

        return response()->json([
            'status' => true,
            'cities' => $cities,
        ]);
    }
}
