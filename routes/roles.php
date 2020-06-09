<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'roles' => '\\' . config('varbox.bindings.controllers.roles_controller', \Varbox\Controllers\RolesController::class),
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
        'prefix' => 'roles',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.roles.index', 'uses' => $controllers['roles'] . '@index', 'permissions' => 'roles-list']);
        Route::get('create', ['as' => 'admin.roles.create', 'uses' => $controllers['roles'] . '@create', 'permissions' => 'roles-add']);
        Route::post('store', ['as' => 'admin.roles.store', 'uses' => $controllers['roles'] . '@store', 'permissions' => 'roles-add']);
        Route::get('edit/{role}', ['as' => 'admin.roles.edit', 'uses' => $controllers['roles'] . '@edit', 'permissions' => 'roles-edit']);
        Route::put('update/{role}', ['as' => 'admin.roles.update', 'uses' => $controllers['roles'] . '@update', 'permissions' => 'roles-edit']);
        Route::delete('destroy/{role}', ['as' => 'admin.roles.destroy', 'uses' => $controllers['roles'] . '@destroy', 'permissions' => 'roles-delete']);
        Route::get('csv', ['as' => 'admin.roles.csv', 'uses' => $controllers['roles'] . '@csv', 'permissions' => 'roles-export']);
    });
});
