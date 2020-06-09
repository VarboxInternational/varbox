<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'cities' => '\\' . config('varbox.bindings.controllers.cities_controller', \Varbox\Controllers\CitiesController::class),
];

Route::group([
    'prefix' => config('varbox.admin.prefix', 'admin'),
    'middleware' => [
        'web',
        'varbox.auth.session:admin',
        'varbox.authenticated:admin',
        'varbox.check.roles',
        'varbox.check.permissions',
    ]
], function () use ($controllers) {
    Route::group([
        'prefix' => 'cities',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.cities.index', 'uses' => $controllers['cities'] . '@index', 'permissions' => 'cities-list']);
        Route::get('create', ['as' => 'admin.cities.create', 'uses' => $controllers['cities'] . '@create', 'permissions' => 'cities-add']);
        Route::post('store', ['as' => 'admin.cities.store', 'uses' => $controllers['cities'] . '@store', 'permissions' => 'cities-add']);
        Route::get('edit/{city}', ['as' => 'admin.cities.edit', 'uses' => $controllers['cities'] . '@edit', 'permissions' => 'cities-edit']);
        Route::put('update/{city}', ['as' => 'admin.cities.update', 'uses' => $controllers['cities'] . '@update', 'permissions' => 'cities-edit']);
        Route::delete('destroy/{city}', ['as' => 'admin.cities.destroy', 'uses' => $controllers['cities'] . '@destroy', 'permissions' => 'cities-delete']);
        Route::get('csv', ['as' => 'admin.cities.csv', 'uses' => $controllers['cities'] . '@csv', 'permissions' => 'cities-export']);
        Route::get('get/{country?}/{state?}', ['as' => 'admin.cities.get', 'uses' => $controllers['cities'] . '@get']);
    });
});
