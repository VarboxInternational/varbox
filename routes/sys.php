<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'configs' => '\\' . config('varbox.sys.binding.controllers.configs_controller', \Varbox\Controllers\ConfigsController::class),
    'errors' => '\\' . config('varbox.sys.binding.controllers.errors_controller', \Varbox\Controllers\ErrorsController::class),
    'backups' => '\\' . config('varbox.sys.binding.controllers.backups_controller', \Varbox\Controllers\BackupsController::class),
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
    /*
     * Configs.
     */
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

    /**
     * Errors.
     */
    Route::group([
        'prefix' => 'errors',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.errors.index', 'uses' => $controllers['errors'] . '@index', 'permissions' => 'errors-list']);
        Route::get('show/{error}', ['as' => 'admin.errors.show', 'uses' => $controllers['errors'] . '@show', 'permissions' => 'errors-view']);
        Route::delete('destroy/{error}', ['as' => 'admin.errors.destroy', 'uses' => $controllers['errors'] . '@destroy', 'permissions' => 'errors-delete']);
        Route::delete('clear', ['as' => 'admin.errors.clear', 'uses' => $controllers['errors'] . '@clear', 'permissions' => 'errors-delete']);
    });

    /**
     * Backups.
     */
    Route::group([
        'prefix' => 'backups',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.backups.index', 'uses' => $controllers['backups'] . '@index', 'permissions' => 'backups-list']);
        Route::post('create', ['as' => 'admin.backups.create', 'uses' => $controllers['backups'] . '@create', 'permissions' => 'backups-create']);
        Route::get('download/{backup}', ['as' => 'admin.backups.download', 'uses' => $controllers['backups'] . '@download', 'permissions' => 'backups-download']);
        Route::delete('destroy/{backup}', ['as' => 'admin.backups.destroy', 'uses' => $controllers['backups'] . '@destroy', 'permissions' => 'backups-delete']);
        Route::delete('clear', ['as' => 'admin.backups.clear', 'uses' => $controllers['backups'] . '@clear', 'permissions' => 'backups-delete']);
    });
});