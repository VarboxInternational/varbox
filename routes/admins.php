<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'admins' => '\\' . config('varbox.bindings.controllers.admins_controller', \Varbox\Controllers\AdminsController::class),
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
        'prefix' => 'admins',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.admins.index', 'uses' => $controllers['admins'] . '@index', 'permissions' => 'admins-list']);
        Route::get('create', ['as' => 'admin.admins.create', 'uses' => $controllers['admins'] . '@create', 'permissions' => 'admins-add']);
        Route::post('store', ['as' => 'admin.admins.store', 'uses' => $controllers['admins'] . '@store', 'permissions' => 'admins-add']);
        Route::get('edit/{user}', ['as' => 'admin.admins.edit', 'uses' => $controllers['admins'] . '@edit', 'permissions' => 'admins-edit']);
        Route::put('update/{user}', ['as' => 'admin.admins.update', 'uses' => $controllers['admins'] . '@update', 'permissions' => 'admins-edit']);
        Route::delete('destroy/{user}', ['as' => 'admin.admins.destroy', 'uses' => $controllers['admins'] . '@destroy', 'permissions' => 'admins-delete']);
    });
});