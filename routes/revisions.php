<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'revisions' => '\\' . config('varbox.bindings.controllers.revisions_controller', \Varbox\Controllers\RevisionsController::class),
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
        'prefix' => 'revisions',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.revisions.index', 'uses' => $controllers['revisions'] . '@index', 'permissions' => 'revisions-list']);
        Route::delete('destroy/{revision}', ['as' => 'admin.revisions.destroy', 'uses' => $controllers['revisions'] . '@destroy', 'permissions' => 'revisions-delete']);
        Route::match(['post', 'put'], 'rollback/{revision}', ['as' => 'admin.revisions.rollback', 'uses' => $controllers['revisions'] . '@rollback', 'permissions' => 'revisions-rollback']);
    });
});