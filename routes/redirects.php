<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'redirects' => '\\' . config('varbox.bindings.controllers.redirects_controller', \Varbox\Controllers\RedirectsController::class),
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
        'prefix' => 'redirects',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.redirects.index', 'uses' => $controllers['redirects'] . '@index', 'permissions' => 'redirects-list']);
        Route::get('create', ['as' => 'admin.redirects.create', 'uses' => $controllers['redirects'] . '@create', 'permissions' => 'redirects-add']);
        Route::post('store', ['as' => 'admin.redirects.store', 'uses' => $controllers['redirects'] . '@store', 'permissions' => 'redirects-add']);
        Route::get('edit/{redirect}', ['as' => 'admin.redirects.edit', 'uses' => $controllers['redirects'] . '@edit', 'permissions' => 'redirects-edit']);
        Route::put('update/{redirect}', ['as' => 'admin.redirects.update', 'uses' => $controllers['redirects'] . '@update', 'permissions' => 'redirects-edit']);
        Route::delete('destroy/{redirect}', ['as' => 'admin.redirects.destroy', 'uses' => $controllers['redirects'] . '@destroy', 'permissions' => 'redirects-delete']);
        Route::delete('delete-all', ['as' => 'admin.redirects.delete_all', 'uses' => $controllers['redirects'] . '@deleteAll', 'permissions' => 'redirects-delete']);
    });
});
