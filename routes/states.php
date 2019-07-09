<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'states' => '\\' . config('varbox.varbox-binding.controllers.states_controller', \Varbox\Controllers\StatesController::class),
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
});
