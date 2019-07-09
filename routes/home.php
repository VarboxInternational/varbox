<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'dashboard' => '\\' . config('varbox.varbox-binding.controllers.dashboard_controller', \Varbox\Controllers\DashboardController::class),
];

Route::group([
    'middleware' => [
        'web',
        'varbox.auth.session:admin',
        'varbox.authenticated:admin',
        'varbox.check.roles',
        'varbox.check.permissions',
    ]
], function () use ($controllers) {
    Route::get('admin', ['as' => 'admin', 'uses' => $controllers['dashboard'] . '@index']);
});