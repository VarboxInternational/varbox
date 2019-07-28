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
        Route::get('/', ['as' => 'admin.revisions.get', 'uses' => $controllers['revisions'] . '@getRevisions', 'permissions' => 'revisions-list']);
        Route::match(['post', 'put'], 'rollback/{revision}', ['as' => 'admin.revisions.rollback', 'uses' => $controllers['revisions'] . '@rollbackRevision', 'permissions' => 'revisions-rollback']);
        Route::delete('destroy/{revision}', ['as' => 'admin.revisions.remove', 'uses' => $controllers['revisions'] . '@removeRevision', 'permissions' => 'revisions-delete']);
    });
});