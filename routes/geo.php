<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'countries' => '\\' . config('varbox.varbox-binding.controllers.countries_controller', \Varbox\Controllers\CountriesController::class),
    'states' => '\\' . config('varbox.varbox-binding.controllers.states_controller', \Varbox\Controllers\StatesController::class),
    'cities' => '\\' . config('varbox.varbox-binding.controllers.cities_controller', \Varbox\Controllers\CitiesController::class),
    'addresses' => '\\' . config('varbox.varbox-binding.controllers.addresses_controller', \Varbox\Controllers\AddressesController::class),
];

Route::group([
    'prefix' => 'admin',
    'middleware' => [
        'web',
        'varbox.auth.session:admin',
        'varbox.authenticated:admin',
        'varbox.check.roles',
        'varbox.check.permissions',
    ]
], function () use ($controllers) {
    /**
     * CRUD Countries.
     */
    Route::group([
        'prefix' => 'countries',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.countries.index', 'uses' => $controllers['countries'] . '@index', 'permissions' => 'countries-list']);
        Route::get('create', ['as' => 'admin.countries.create', 'uses' => $controllers['countries'] . '@create', 'permissions' => 'countries-add']);
        Route::post('store', ['as' => 'admin.countries.store', 'uses' => $controllers['countries'] . '@store', 'permissions' => 'countries-add']);
        Route::get('edit/{country}', ['as' => 'admin.countries.edit', 'uses' => $controllers['countries'] . '@edit', 'permissions' => 'countries-edit']);
        Route::put('update/{country}', ['as' => 'admin.countries.update', 'uses' => $controllers['countries'] . '@update', 'permissions' => 'countries-edit']);
        Route::delete('destroy/{country}', ['as' => 'admin.countries.destroy', 'uses' => $controllers['countries'] . '@destroy', 'permissions' => 'countries-delete']);
    });

    /**
     * CRUD States.
     */
    Route::group([
        'prefix' => 'states',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.states.index', 'uses' => $controllers['states'] . '@index', 'permissions' => 'states-list']);
        Route::get('create', ['as' => 'admin.states.create', 'uses' => $controllers['states'] . '@create', 'permissions' => 'states-add']);
        Route::post('store', ['as' => 'admin.states.store', 'uses' => $controllers['states'] . '@store', 'permissions' => 'states-add']);
        Route::get('edit/{state}', ['as' => 'admin.states.edit', 'uses' => $controllers['states'] . '@edit', 'permissions' => 'states-edit']);
        Route::put('update/{state}', ['as' => 'admin.states.update', 'uses' => $controllers['states'] . '@update', 'permissions' => 'states-edit']);
        Route::delete('destroy/{state}', ['as' => 'admin.states.destroy', 'uses' => $controllers['states'] . '@destroy', 'permissions' => 'states-delete']);
        Route::get('get/{country?}', ['as' => 'admin.states.get', 'uses' => $controllers['states'] . '@get']);
    });

    /**
     * CRUD Cities.
     */
    Route::group([
        'prefix' => 'cities',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.cities.index', 'uses' => $controllers['cities'] . '@index', 'permissions' => 'cities-list']);
        Route::get('create', ['as' => 'admin.cities.create', 'uses' => $controllers['cities'] . '@create', 'permissions' => 'cities-add']);
        Route::post('store', ['as' => 'admin.cities.store', 'uses' => $controllers['cities'] . '@store', 'permissions' => 'cities-add']);
        Route::get('edit/{city}', ['as' => 'admin.cities.edit', 'uses' => $controllers['cities'] . '@edit', 'permissions' => 'cities-edit']);
        Route::put('update/{city}', ['as' => 'admin.cities.update', 'uses' => $controllers['cities'] . '@update', 'permissions' => 'cities-edit']);
        Route::delete('destroy/{city}', ['as' => 'admin.cities.destroy', 'uses' => $controllers['cities'] . '@destroy', 'permissions' => 'cities-delete']);
        Route::get('get/{country?}/{state?}', ['as' => 'admin.cities.get', 'uses' => $controllers['cities'] . '@get']);
    });

    /**
     * CRUD Addresses.
     */
    Route::group([
        'prefix' => 'users/{user}/addresses',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.addresses.index', 'uses' => $controllers['addresses'] . '@index', 'permissions' => 'addresses-list']);
        Route::get('create', ['as' => 'admin.addresses.create', 'uses' => $controllers['addresses'] . '@create', 'permissions' => 'addresses-add']);
        Route::post('store', ['as' => 'admin.addresses.store', 'uses' => $controllers['addresses'] . '@store', 'permissions' => 'addresses-add']);
        Route::get('edit/{address}', ['as' => 'admin.addresses.edit', 'uses' => $controllers['addresses'] . '@edit', 'permissions' => 'addresses-edit']);
        Route::put('update/{address}', ['as' => 'admin.addresses.update', 'uses' => $controllers['addresses'] . '@update', 'permissions' => 'addresses-edit']);
        Route::delete('destroy/{address}', ['as' => 'admin.addresses.destroy', 'uses' => $controllers['addresses'] . '@destroy', 'permissions' => 'addresses-delete']);
    });
});
