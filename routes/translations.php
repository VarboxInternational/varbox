<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'translations' => '\\' . config('varbox.bindings.controllers.pages_controller', \Varbox\Controllers\TranslationsController::class),
];

Route::group([
    'prefix' => 'translations',
], function () use ($controllers) {
    Route::get('/', ['as' => 'admin.translations.index', 'uses' => $controllers['translations'] . '@index', 'permissions' => 'translations-list']);
    Route::get('create', ['as' => 'admin.translations.create', 'uses' => $controllers['translations'] . '@create', 'permissions' => 'translations-add']);
    Route::post('store', ['as' => 'admin.translations.store', 'uses' => $controllers['translations'] . '@store', 'permissions' => 'translations-add']);
    Route::get('edit/{translation}', ['as' => 'admin.translations.edit', 'uses' => $controllers['translations'] . '@edit', 'permissions' => 'translations-edit']);
    Route::put('update/{translation}', ['as' => 'admin.translations.update', 'uses' => $controllers['translations'] . '@update', 'permissions' => 'translations-edit']);
    Route::delete('destroy/{translation}', ['as' => 'admin.translations.destroy', 'uses' => $controllers['translations'] . '@destroy', 'permissions' => 'translations-delete']);
    Route::post('import', ['as' => 'admin.translations.import', 'uses' => $controllers['translations'] . '@import', 'permissions' => 'translations-import']);
    Route::post('export', ['as' => 'admin.translations.export', 'uses' => $controllers['translations'] . '@export', 'permissions' => 'translations-export']);
    Route::post('sync', ['as' => 'admin.translations.sync', 'uses' => $controllers['translations'] . '@sync', 'permissions' => 'translations-sync']);
    Route::delete('clear', ['as' => 'admin.translations.clear', 'uses' => $controllers['translations'] . '@clear', 'permissions' => 'translations-clear']);
});
