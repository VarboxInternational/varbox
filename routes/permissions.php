<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'permissions' => '\\' . config('varbox.bindings.controllers.permissions_controller', \Varbox\Controllers\PermissionsController::class),
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
        'prefix' => 'permissions',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.permissions.index', 'uses' => $controllers['permissions'] . '@index', 'permissions' => 'permissions-list']);
        Route::get('create', ['as' => 'admin.permissions.create', 'uses' => $controllers['permissions'] . '@create', 'permissions' => 'permissions-add']);
        Route::post('store', ['as' => 'admin.permissions.store', 'uses' => $controllers['permissions'] . '@store', 'permissions' => 'permissions-add']);
        Route::get('edit/{permission}', ['as' => 'admin.permissions.edit', 'uses' => $controllers['permissions'] . '@edit', 'permissions' => 'permissions-edit']);
        Route::put('update/{permission}', ['as' => 'admin.permissions.update', 'uses' => $controllers['permissions'] . '@update', 'permissions' => 'permissions-edit']);
        Route::delete('destroy/{permission}', ['as' => 'admin.permissions.destroy', 'uses' => $controllers['permissions'] . '@destroy', 'permissions' => 'permissions-delete']);
    });
});