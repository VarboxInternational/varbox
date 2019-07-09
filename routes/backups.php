<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'backups' => '\\' . config('varbox.bindings.controllers.backups_controller', \Varbox\Controllers\BackupsController::class),
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
        'prefix' => 'backups',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.backups.index', 'uses' => $controllers['backups'] . '@index', 'permissions' => 'backups-list']);
        Route::post('store', ['as' => 'admin.backups.store', 'uses' => $controllers['backups'] . '@store', 'permissions' => 'backups-create']);
        Route::get('download/{backup}', ['as' => 'admin.backups.download', 'uses' => $controllers['backups'] . '@download', 'permissions' => 'backups-download']);
        Route::delete('destroy/{backup}', ['as' => 'admin.backups.destroy', 'uses' => $controllers['backups'] . '@destroy', 'permissions' => 'backups-delete']);
        Route::delete('delete-all', ['as' => 'admin.backups.delete_all', 'uses' => $controllers['backups'] . '@deleteAll', 'permissions' => 'backups-delete']);
    });
});