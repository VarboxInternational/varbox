<?php

namespace Varbox\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Varbox\Contracts\AddressFilterContract;
use Varbox\Contracts\AddressModelContract;
use Varbox\Contracts\CityModelContract;
use Varbox\Contracts\CountryModelContract;
use Varbox\Contracts\StateModelContract;
use Varbox\Contracts\UserModelContract;
use Varbox\Requests\AddressRequest;
use Varbox\Sorts\AddressSort;
use Varbox\Traits\CanCrud;

class AddressesController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    use CanCrud;

    /**
     * @var AddressModelContract
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
     * @var CityModelContract
     */
    protected $city;

    /**
     * @param AddressModelContract $model
     * @param CountryModelContract $country
     * @param StateModelContract $state
     * @param CityModelContract $city
     */
    public function __construct(AddressModelContract $model, CountryModelContract $country, StateModelContract $state, CityModelContract $city)
    {

        $this->model = $model;
        $this->country = $country;
        $this->state = $state;
        $this->city = $city;
    }

    /**
     * @param Request $request
     * @param AddressFilterContract $filter
     * @param AddressSort $sort
     * @param UserModelContract $user
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function index(Request $request, AddressFilterContract $filter, AddressSort $sort, UserModelContract $user)
    {
        return $this->_index(function () use ($request, $filter, $sort, $user) {
            $this->items = $this->model->ofUser($user)
                ->filtered($request->all(), $filter)
                ->sorted($request->all(), $sort)
                ->paginate(config('varbox.crud.per_page', 30));

            $this->title = 'Addresses';
            $this->view = view('varbox::admin.addresses.index');
            $this->vars = [
                'user' => $user,
                'countries' => $this->country->alphabetically()->get(),
            ];
        });
    }

    /**
     * @param UserModelContract $user
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function create(UserModelContract $user)
    {
        return $this->_create(function () use ($user) {
            $this->title = 'Add Address';
            $this->view = view('varbox::admin.addresses.add');
            $this->vars = [
                'user' => $user,
                'countries' => $this->country->alphabetically()->get(),
            ];
        });
    }

    /**
     * @param Request $request
     * @param UserModelContract $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(Request $request, UserModelContract $user)
    {
        app(config('varbox.bindings.form_requests.address_form_request', AddressRequest::class));

        return $this->_store(function () use ($request, $user) {
            $this->item = $this->model->create($request->all());
            $this->redirect = redirect()->route('admin.addresses.index', $user->getKey());
        }, $request);
    }

    /**
     * @param UserModelContract $user
     * @param AddressModelContract $address
     * @return \Illuminate\View\View
     * @throws \Exception
     */
    public function edit(UserModelContract $user, AddressModelContract $address)
    {
        return $this->_edit(function () use ($user, $address) {
            $this->item = $address;
            $this->title = 'Edit Address';
            $this->view = view('varbox::admin.addresses.edit');
            $this->vars = [
                'user' => $user,
                'countries' => $this->country->alphabetically()->get(),
                'states' => $this->state->alphabetically()
                    ->fromCountry($address->country_id)
                    ->get(),
                'cities' => $this->city->alphabetically()
                    ->fromCountry($address->country_id)
                    ->fromState($address->state_id)
                    ->get(),
            ];
        });
    }

    /**
     * @param Request $request
     * @param AddressModelContract $address
     * @param UserModelContract $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function update(Request $request, UserModelContract $user, AddressModelContract $address)
    {
        app(config('varbox.bindings.form_requests.address_form_request', AddressRequest::class));

        return $this->_update(function () use ($request, $user, $address) {
            $this->item = $address;
            $this->redirect = redirect()->route('admin.addresses.index', $user->getKey());

            $this->item->update($request->all());
        }, $request);
    }

    /**
     * @param AddressModelContract $address
     * @param UserModelContract $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(UserModelContract $user, AddressModelContract $address)
    {
        return $this->_destroy(function () use ($user, $address) {
            $this->item = $address;
            $this->redirect = redirect()->route('admin.addresses.index', $user->getKey());

            $this->item->delete();
        });
    }
}
