<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'users' => '\\' . config('varbox.bindings.controllers.users_controller', \Varbox\Controllers\UsersController::class),
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
        'prefix' => 'users',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.users.index', 'uses' => $controllers['users'] . '@index', 'permissions' => 'users-list']);
        Route::get('create', ['as' => 'admin.users.create', 'uses' => $controllers['users'] . '@create', 'permissions' => 'users-add']);
        Route::post('store', ['as' => 'admin.users.store', 'uses' => $controllers['users'] . '@store', 'permissions' => 'users-add']);
        Route::get('edit/{user}', ['as' => 'admin.users.edit', 'uses' => $controllers['users'] . '@edit', 'permissions' => 'users-edit']);
        Route::put('update/{user}', ['as' => 'admin.users.update', 'uses' => $controllers['users'] . '@update', 'permissions' => 'users-edit']);
        Route::delete('destroy/{user}', ['as' => 'admin.users.destroy', 'uses' => $controllers['users'] . '@destroy', 'permissions' => 'users-delete']);
        Route::post('impersonate/{user}', ['as' => 'admin.users.impersonate', 'uses' => $controllers['users'] . '@impersonate', 'permissions' => 'users-impersonate']);
    });
});
