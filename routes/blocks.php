<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'blocks' => '\\' . config('varbox.bindings.controllers.blocks_controller', \Varbox\Controllers\BlocksController::class),
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
        'prefix' => 'blocks',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.blocks.index', 'uses' => $controllers['blocks'] . '@index', 'permissions' => 'blocks-list']);
        Route::get('create/{type?}', ['as' => 'admin.blocks.create', 'uses' => $controllers['blocks'] . '@create', 'permissions' => 'blocks-add']);
        Route::post('store', ['as' => 'admin.blocks.store', 'uses' => $controllers['blocks'] . '@store', 'permissions' => 'blocks-add']);
        Route::get('edit/{block}', ['as' => 'admin.blocks.edit', 'uses' => $controllers['blocks'] . '@edit', 'permissions' => 'blocks-edit']);
        Route::put('update/{block}', ['as' => 'admin.blocks.update', 'uses' => $controllers['blocks'] . '@update', 'permissions' => 'blocks-edit']);
        Route::delete('destroy/{block}', ['as' => 'admin.blocks.destroy', 'uses' => $controllers['blocks'] . '@destroy', 'permissions' => 'blocks-delete']);

        /**
         * Draft Actions.
         */
        Route::post('draft/{block?}', ['as' => 'admin.blocks.draft', 'uses' => $controllers['blocks'] . '@saveDraft', 'permissions' => 'blocks-draft']);
        Route::put('publish/{block}', ['as' => 'admin.blocks.publish', 'uses' => $controllers['blocks'] . '@publishDraft', 'permissions' => 'blocks-publish']);

        /**
         * Revision Actions.
         */
        Route::get('revision/{revision}', ['as' => 'admin.blocks.revision', 'uses' => $controllers['blocks'] . '@showRevision', 'permissions' => 'blocks-edit']);

        /**
         * Duplicate Actions
         */
        Route::post('duplicate/{block}', ['as' => 'admin.blocks.duplicate', 'uses' => $controllers['blocks'] . '@duplicate', 'permissions' => 'blocks-duplicate']);

        /**
         * Ajax Actions.
         */
        Route::get('get', ['as' => 'admin.blocks.get', 'uses' => $controllers['blocks'] . '@get', 'permissions' => 'blocks-show']);
        Route::post('row', ['as' => 'admin.blocks.row', 'uses' => $controllers['blocks'] . '@row', 'permissions' => 'blocks-show']);
    });
});
