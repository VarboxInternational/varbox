<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'drafts' => '\\' . config('varbox.bindings.controllers.drafts_controller', \Varbox\Controllers\DraftsController::class),
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
        'prefix' => 'drafts',
    ], function () use ($controllers) {
        Route::post('save', ['as' => 'admin.drafts.save', 'uses' => $controllers['drafts'] . '@save', 'permissions' => 'drafts-save']);
        Route::put('publish', ['as' => 'admin.drafts.publish', 'uses' => $controllers['drafts'] . '@publish', 'permissions' => 'drafts-publish']);
    });
});
