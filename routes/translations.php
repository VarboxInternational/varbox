<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'translations' => '\\' . config('varbox.bindings.controllers.translations_controller', \Varbox\Controllers\TranslationsController::class),
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
        'prefix' => 'translations',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.translations.index', 'uses' => $controllers['translations'] . '@index', 'permissions' => 'translations-list']);
        Route::get('edit/{translation}', ['as' => 'admin.translations.edit', 'uses' => $controllers['translations'] . '@edit', 'permissions' => 'translations-edit']);
        Route::put('update/{translation}', ['as' => 'admin.translations.update', 'uses' => $controllers['translations'] . '@update', 'permissions' => 'translations-edit']);
        Route::delete('destroy/{translation}', ['as' => 'admin.translations.destroy', 'uses' => $controllers['translations'] . '@destroy', 'permissions' => 'translations-delete']);
        Route::post('import', ['as' => 'admin.translations.import', 'uses' => $controllers['translations'] . '@import', 'permissions' => 'translations-import']);
        Route::post('export', ['as' => 'admin.translations.export', 'uses' => $controllers['translations'] . '@export', 'permissions' => 'translations-export']);
        Route::post('translate', ['as' => 'admin.translations.translate', 'uses' => $controllers['translations'] . '@translate', 'permissions' => 'translations-translate']);
        Route::delete('clear', ['as' => 'admin.translations.clear', 'uses' => $controllers['translations'] . '@clear', 'permissions' => 'translations-delete']);
    });
});
