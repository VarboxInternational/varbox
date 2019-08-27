<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'pages' => '\\' . config('varbox.bindings.controllers.pages_controller', \Varbox\Controllers\PagesController::class),
    'tree' => '\\' . config('varbox.bindings.controllers.pages_tree_controller', \Varbox\Controllers\PagesTreeController::class),
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
        'prefix' => 'pages',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.pages.index', 'uses' => $controllers['pages'] . '@index', 'permissions' => 'pages-list']);
        Route::get('create/{pageParent?}', ['as' => 'admin.pages.create', 'uses' => $controllers['pages'] . '@create', 'permissions' => 'pages-add']);
        Route::post('store/{pageParent?}', ['as' => 'admin.pages.store', 'uses' => $controllers['pages'] . '@store', 'permissions' => 'pages-add']);
        Route::get('edit/{page}', ['as' => 'admin.pages.edit', 'uses' => $controllers['pages'] . '@edit', 'permissions' => 'pages-edit']);
        Route::put('update/{page}', ['as' => 'admin.pages.update', 'uses' => $controllers['pages'] . '@update', 'permissions' => 'pages-edit']);
        Route::delete('destroy/{page}', ['as' => 'admin.pages.destroy', 'uses' => $controllers['pages'] . '@destroy', 'permissions' => 'pages-delete']);

        /**
         * Draft Actions.
         */
        Route::post('draft/{page?}', ['as' => 'admin.pages.draft', 'uses' => $controllers['pages'] . '@saveDraft', 'permissions' => 'pages-draft']);
        Route::put('publish/{page}', ['as' => 'admin.pages.publish', 'uses' => $controllers['pages'] . '@publishDraft', 'permissions' => 'pages-publish']);

        /**
         * Revision Actions.
         */
        Route::get('revision/{revision}', ['as' => 'admin.pages.revision', 'uses' => $controllers['pages'] . '@showRevision', 'permissions' => 'pages-edit']);

        /**
         * Duplicate Actions
         */
        Route::post('duplicate/{page}', ['as' => 'admin.pages.duplicate', 'uses' => $controllers['pages'] . '@duplicate', 'permissions' => 'pages-duplicate']);

        /**
         * Preview Actions.
         */
        Route::match(['post', 'put'], 'preview/{page?}', ['as' => 'admin.pages.preview', 'uses' => $controllers['pages'] . '@preview', 'permissions' => 'pages-preview']);

        /**
         * Tree Actions.
         */
        Route::group([
            'prefix' => 'tree'
        ], function () use ($controllers) {
            Route::get('load/{parent?}', ['as' => 'admin.pages.tree.load', 'uses' => $controllers['tree'] . '@loadNodes']);
            Route::get('list/{parent?}', ['as' => 'admin.pages.tree.list', 'uses' => $controllers['tree'] . '@listItems']);
            Route::put('fix', ['as' => 'admin.pages.tree.fix', 'uses' => $controllers['tree'] . '@fixTree']);
            Route::post('sort', ['as' => 'admin.pages.tree.sort', 'uses' => $controllers['tree'] . '@sortItems']);
            Route::post('url', ['as' => 'admin.pages.tree.url', 'uses' => $controllers['tree'] . '@refreshUrls']);
        });
    });
});
