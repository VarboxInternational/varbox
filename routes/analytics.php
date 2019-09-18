<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'analytics' => '\\' . config('varbox.bindings.controllers.analytics_controller', \Varbox\Controllers\AnalyticsController::class),
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
        'prefix' => 'analytics',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.analytics.show', 'uses' => $controllers['analytics'] . '@show', 'permissions' => 'analytics-view']);
        Route::put('update/{analytics?}', ['as' => 'admin.analytics.update', 'uses' => $controllers['analytics'] . '@update', 'permissions' => 'analytics-edit']);
    });
});
