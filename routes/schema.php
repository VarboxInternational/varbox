<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'schema' => '\\' . config('varbox.bindings.controllers.schema_controller', \Varbox\Controllers\SchemaController::class),
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
        'prefix' => 'schema',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.schema.index', 'uses' => $controllers['schema'] . '@index', 'permissions' => 'schema-list']);
        Route::get('create/{type?}', ['as' => 'admin.schema.create', 'uses' => $controllers['schema'] . '@create', 'permissions' => 'schema-add']);
        Route::post('store', ['as' => 'admin.schema.store', 'uses' => $controllers['schema'] . '@store', 'permissions' => 'schema-add']);
        Route::get('edit/{schema}', ['as' => 'admin.schema.edit', 'uses' => $controllers['schema'] . '@edit', 'permissions' => 'schema-edit']);
        Route::put('update/{schema}', ['as' => 'admin.schema.update', 'uses' => $controllers['schema'] . '@update', 'permissions' => 'schema-edit']);
        Route::delete('destroy/{schema}', ['as' => 'admin.schema.destroy', 'uses' => $controllers['schema'] . '@destroy', 'permissions' => 'schema-delete']);
    });
});
