<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'dashboard' => '\\' . config('varbox.bindings.controllers.dashboard_controller', \Varbox\Controllers\DashboardController::class),
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
    Route::get('/', ['as' => 'admin', 'uses' => $controllers['dashboard'] . '@index']);
});
