<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'countries' => '\\' . config('varbox.bindings.controllers.countries_controller', \Varbox\Controllers\CountriesController::class),
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
        'prefix' => 'countries',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.countries.index', 'uses' => $controllers['countries'] . '@index', 'permissions' => 'countries-list']);
        Route::get('create', ['as' => 'admin.countries.create', 'uses' => $controllers['countries'] . '@create', 'permissions' => 'countries-add']);
        Route::post('store', ['as' => 'admin.countries.store', 'uses' => $controllers['countries'] . '@store', 'permissions' => 'countries-add']);
        Route::get('edit/{country}', ['as' => 'admin.countries.edit', 'uses' => $controllers['countries'] . '@edit', 'permissions' => 'countries-edit']);
        Route::put('update/{country}', ['as' => 'admin.countries.update', 'uses' => $controllers['countries'] . '@update', 'permissions' => 'countries-edit']);
        Route::delete('destroy/{country}', ['as' => 'admin.countries.destroy', 'uses' => $controllers['countries'] . '@destroy', 'permissions' => 'countries-delete']);
    });
});
