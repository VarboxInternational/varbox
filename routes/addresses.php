<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'addresses' => '\\' . config('varbox.bindings.controllers.addresses_controller', \Varbox\Controllers\AddressesController::class),
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
