<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'froala' => '\\' . config('varbox.bindings.controllers.froala_controller', \Varbox\Controllers\FroalaController::class),
];

Route::group([
    'prefix' => 'froala',
    'middleware' => [
        'web',
        'varbox.auth.session:admin',
        'varbox.authenticated:admin',
    ]
], function () use ($controllers) {
    Route::group([
        'prefix' => 'upload',
    ], function () use ($controllers) {
        Route::post('image', ['as' => 'froala.upload.image', 'uses' => $controllers['froala'] . '@uploadImage']);
    });
});