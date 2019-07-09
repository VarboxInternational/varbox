<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'activity' => '\\' . config('varbox.bindings.controllers.activity_controller', \Varbox\Controllers\ActivityController::class),
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
        'prefix' => 'activity',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.activity.index', 'uses' => $controllers['activity'] . '@index', 'permissions' => 'activity-list']);
        Route::delete('destroy/{activity}', ['as' => 'admin.activity.destroy', 'uses' => $controllers['activity'] . '@destroy', 'permissions' => 'activity-delete']);
        Route::delete('delete', ['as' => 'admin.activity.delete', 'uses' => $controllers['activity'] . '@delete', 'permissions' => 'activity-delete']);
        Route::delete('clean', ['as' => 'admin.activity.clean', 'uses' => $controllers['activity'] . '@clean', 'permissions' => 'activity-delete']);
    });
});