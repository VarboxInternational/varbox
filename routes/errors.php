<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'errors' => '\\' . config('varbox.varbox-binding.controllers.errors_controller', \Varbox\Controllers\ErrorsController::class),
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
        'prefix' => 'errors',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.errors.index', 'uses' => $controllers['errors'] . '@index', 'permissions' => 'errors-list']);
        Route::get('show/{error}', ['as' => 'admin.errors.show', 'uses' => $controllers['errors'] . '@show', 'permissions' => 'errors-view']);
        Route::delete('destroy/{error}', ['as' => 'admin.errors.destroy', 'uses' => $controllers['errors'] . '@destroy', 'permissions' => 'errors-delete']);
        Route::delete('delete-all', ['as' => 'admin.errors.delete_all', 'uses' => $controllers['errors'] . '@deleteAll', 'permissions' => 'errors-delete']);
    });
});