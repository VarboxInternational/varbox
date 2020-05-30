<?php

namespace Varbox\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\CountryModelContract;
use Varbox\Contracts\StateModelContract;
use Varbox\Filters\StateFilter;
use Varbox\Requests\StateRequest;
use Varbox\Sorts\StateSort;
use Varbox\Traits\CanCrud;

class StatesController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var StateModelContract
     */
    protected $model;

    /**
     * @var CountryModelContract
     */
    protected $country;

    /**
     * CountriesController constructor.
     *
     * @param StateModelContract $model
     * @param CountryModelContract $country
     */
    public function __construct(StateModelContract $model, CountryModelContract $country)
    {
        $this->model = $model;
        $this->country = $country;
    }

    /**
     * @param Request $request
     * @param StateFilter $filter
     * @param StateSort $sort
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, StateFilter $filter, StateSort $sort)
    {
        return $this->_index(function () use ($request, $filter, $sort) {
            $query = $this->model->with('country')->filtered($request->all(), $filter);

            if ($request->filled('sort')) {
                $query->sorted($request->all(), $sort);
            } else {
                $query->alphabetically();
            }

            $this->items = $query->paginate(8);
            $this->title = 'States';
            $this->view = view('varbox::admin.states.index');
            $this->vars = [
                'countries' => $this->country->alphabetically()->get(),
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
            $this->title = 'Add State';
            $this->view = view('varbox::admin.states.add');
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
        app(config('varbox.bindings.form_requests.state_form_request', StateRequest::class));

        return $this->_store(function () use ($request) {
            $this->item = $this->model->create($request->all());
            $this->redirect = redirect()->route('admin.states.index');
        }, $request);
    }

    /**
     * @param StateModelContract $state
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(StateModelContract $state)
    {
        return $this->_edit(function () use ($state) {
            $this->item = $state;
            $this->title = 'Edit State';
            $this->view = view('varbox::admin.states.edit');
            $this->vars = [
                'countries' => $this->country->alphabetically()->get(),
            ];
        });
    }

    /**
     * @param Request $request
     * @param StateModelContract $state
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, StateModelContract $state)
    {
        app(config('varbox.bindings.form_requests.state_form_request', StateRequest::class));

        return $this->_update(function () use ($request, $state) {
            $this->item = $state;
            $this->redirect = redirect()->route('admin.states.index');

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param StateModelContract $state
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(StateModelContract $state)
    {
        return $this->_destroy(function () use ($state) {
            $this->item = $state;
            $this->redirect = redirect()->route('admin.states.index');

            $this->item->delete();
        });
    }

    /**
     * @param Request $request
     * @param CountryModelContract|null $country
     * @return array
     */
    public function get(Request $request, CountryModelContract $country = null)
    {
        if (!$request->ajax()) {
            return response()->json([
                'error' => 'Bad request'
            ], 400);
        }

        $states = $cities = [];
        $query = $this->model->alphabetically();

        if ($country && $country->exists) {
            $query->fromCountry($country->getKey());
        }

        foreach ($query->get() as $index => $state) {
            $states[] = [
                'id' => $state->getKey(),
                'name' => $state->name,
                'code' => $state->code,
            ];

            foreach ($state->cities as $city) {
                $cities[] = [
                    'id' => $city->getKey(),
                    'name' => $city->name,
                ];
            }
        }

        return response()->json([
            'status' => true,
            'states' => $states,
            'cities' => $cities,
        ]);
    }
}
