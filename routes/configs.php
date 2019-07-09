<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'configs' => '\\' . config('varbox.varbox-binding.controllers.configs_controller', \Varbox\Controllers\ConfigsController::class),
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
        'prefix' => 'configs',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.configs.index', 'uses' => $controllers['configs'] . '@index', 'permissions' => 'configs-list']);
        Route::get('create', ['as' => 'admin.configs.create', 'uses' => $controllers['configs'] . '@create', 'permissions' => 'configs-add']);
        Route::post('store', ['as' => 'admin.configs.store', 'uses' => $controllers['configs'] . '@store', 'permissions' => 'configs-add']);
        Route::get('edit/{config}', ['as' => 'admin.configs.edit', 'uses' => $controllers['configs'] . '@edit', 'permissions' => 'configs-edit']);
        Route::put('update/{config}', ['as' => 'admin.configs.update', 'uses' => $controllers['configs'] . '@update', 'permissions' => 'configs-edit']);
        Route::delete('destroy/{config}', ['as' => 'admin.configs.destroy', 'uses' => $controllers['configs'] . '@destroy', 'permissions' => 'configs-delete']);
    });
});