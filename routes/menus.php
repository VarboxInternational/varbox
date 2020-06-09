<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'menus' => '\\' . config('varbox.bindings.controllers.menus_controller', \Varbox\Controllers\MenusController::class),
    'tree' => '\\' . config('varbox.bindings.controllers.menus_tree_controller', \Varbox\Controllers\MenusTreeController::class),
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
        'prefix' => 'menus',
    ], function () use ($controllers) {
        Route::get('locations', ['as' => 'admin.menus.locations', 'uses' => $controllers['menus'] . '@locations', 'permissions' => 'menus-list']);
        Route::get('entity/{type?}', ['as' => 'admin.menus.entity', 'uses' => $controllers['menus'] . '@entity', 'permissions' => 'menus-list']);
        Route::get('route', ['as' => 'admin.menus.route', 'uses' => $controllers['menus'] . '@route', 'permissions' => 'menus-list']);
        Route::get('{location}', ['as' => 'admin.menus.index', 'uses' => $controllers['menus'] . '@index', 'permissions' => 'menus-list']);
        Route::get('{location}/create/{menuParent?}', ['as' => 'admin.menus.create', 'uses' => $controllers['menus'] . '@create', 'permissions' => 'menus-add']);
        Route::post('{location}/store/{menuParent?}', ['as' => 'admin.menus.store', 'uses' => $controllers['menus'] . '@store', 'permissions' => 'menus-add']);
        Route::get('{location}/edit/{menu}', ['as' => 'admin.menus.edit', 'uses' => $controllers['menus'] . '@edit', 'permissions' => 'menus-edit']);
        Route::put('{location}/update/{menu}', ['as' => 'admin.menus.update', 'uses' => $controllers['menus'] . '@update', 'permissions' => 'menus-edit']);
        Route::delete('{location}/destroy/{menu}', ['as' => 'admin.menus.destroy', 'uses' => $controllers['menus'] . '@destroy', 'permissions' => 'menus-delete']);

        /**
         * Export Actions.
         */
        Route::get('{location}/csv', ['as' => 'admin.menus.csv', 'uses' => $controllers['menus'] . '@csv', 'permissions' => 'menus-export']);

        /**
         * Tree Actions.
         */
        Route::group([
            'prefix' => 'tree'
        ], function () use ($controllers) {
            Route::get('{location}/load/{parent?}', ['as' => 'admin.menus.tree.load', 'uses' => $controllers['tree'] . '@loadNodes']);
            Route::get('{location}/list/{parent?}', ['as' => 'admin.menus.tree.list', 'uses' => $controllers['tree'] . '@listItems']);
            Route::put('fix', ['as' => 'admin.menus.tree.fix', 'uses' => $controllers['tree'] . '@fixTree']);
            Route::post('sort', ['as' => 'admin.menus.tree.sort', 'uses' => $controllers['tree'] . '@sortItems']);
        });
    });
});
