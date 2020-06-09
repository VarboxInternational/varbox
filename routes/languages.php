<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'languages' => '\\' . config('varbox.bindings.controllers.languages_controller', \Varbox\Controllers\LanguagesController::class),
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
        'prefix' => 'languages',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.languages.index', 'uses' => $controllers['languages'] . '@index', 'permissions' => 'languages-list']);
        Route::get('create', ['as' => 'admin.languages.create', 'uses' => $controllers['languages'] . '@create', 'permissions' => 'languages-add']);
        Route::post('store', ['as' => 'admin.languages.store', 'uses' => $controllers['languages'] . '@store', 'permissions' => 'languages-add']);
        Route::get('edit/{language}', ['as' => 'admin.languages.edit', 'uses' => $controllers['languages'] . '@edit', 'permissions' => 'languages-edit']);
        Route::put('update/{language}', ['as' => 'admin.languages.update', 'uses' => $controllers['languages'] . '@update', 'permissions' => 'languages-edit']);
        Route::delete('destroy/{language}', ['as' => 'admin.languages.destroy', 'uses' => $controllers['languages'] . '@destroy', 'permissions' => 'languages-delete']);
        Route::get('change/{language}', ['as' => 'admin.languages.change', 'uses' => $controllers['languages'] . '@change', 'permissions' => 'languages-change']);
        Route::get('csv', ['as' => 'admin.languages.csv', 'uses' => $controllers['languages'] . '@csv', 'permissions' => 'languages-export']);
    });
});
