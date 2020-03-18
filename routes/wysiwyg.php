<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'wysiwyg' => '\\' . config('varbox.bindings.controllers.wysiwyg_controller', \Varbox\Controllers\WysiwygController::class),
];

Route::group([
    'prefix' => 'wysiwyg',
    'middleware' => [
        'web',
        'varbox.auth.session:admin',
        'varbox.authenticated:admin',
    ]
], function () use ($controllers) {
    Route::post('upload-image', ['as' => 'wysiwyg.upload_image', 'uses' => $controllers['wysiwyg'] . '@uploadImage']);
});
