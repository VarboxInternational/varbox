<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'upload' => '\\' . config('varbox.bindings.controllers.upload_controller', \Varbox\Controllers\UploadController::class),
    'uploads' => '\\' . config('varbox.bindings.controllers.uploads_controller', \Varbox\Controllers\UploadsController::class),
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
        'prefix' => 'uploads',
    ], function () use ($controllers) {
        Route::post('upload', ['as' => 'admin.uploads.upload', 'uses' => $controllers['upload'] . '@upload', 'permissions' => 'uploads-upload']);
        Route::get('get/{type?}', ['as' => 'admin.uploads.get', 'uses' => $controllers['upload'] . '@get', 'permissions' => 'uploads-list']);
        Route::post('set', ['as' => 'admin.uploads.set', 'uses' => $controllers['upload'] . '@set', 'permissions' => 'uploads-select']);
        Route::get('crop', ['as' => 'admin.uploads.crop', 'uses' => $controllers['upload'] . '@crop', 'permissions' => 'uploads-crop']);
        Route::post('cut', ['as' => 'admin.uploads.cut', 'uses' => $controllers['upload'] . '@cut', 'permissions' => 'uploads-crop']);
    });

    Route::group([
        'prefix' => 'uploads',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.uploads.index', 'uses' => $controllers['uploads'] . '@index', 'permissions' => 'uploads-list']);
        Route::get('download/{upload}', ['as' => 'admin.uploads.download', 'uses' => $controllers['uploads'] . '@download', 'permissions' => 'uploads-download']);
        Route::post('store', ['as' => 'admin.uploads.store', 'uses' => $controllers['uploads'] . '@store', 'permissions' => 'uploads-upload']);
        Route::delete('destroy/{upload}', ['as' => 'admin.uploads.destroy', 'uses' => $controllers['uploads'] . '@destroy', 'permissions' => 'uploads-delete']);
    });
});
