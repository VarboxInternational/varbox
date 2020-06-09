<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'emails' => '\\' . config('varbox.bindings.controllers.emails_controller', \Varbox\Controllers\EmailsController::class),
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
        'prefix' => 'emails',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.emails.index', 'uses' => $controllers['emails'] . '@index', 'permissions' => 'emails-list']);
        Route::get('create/{type?}', ['as' => 'admin.emails.create', 'uses' => $controllers['emails'] . '@create', 'permissions' => 'emails-add']);
        Route::post('store', ['as' => 'admin.emails.store', 'uses' => $controllers['emails'] . '@store', 'permissions' => 'emails-add']);
        Route::get('edit/{email}', ['as' => 'admin.emails.edit', 'uses' => $controllers['emails'] . '@edit', 'permissions' => 'emails-edit']);
        Route::put('update/{email}', ['as' => 'admin.emails.update', 'uses' => $controllers['emails'] . '@update', 'permissions' => 'emails-edit']);
        Route::delete('destroy/{email}', ['as' => 'admin.emails.destroy', 'uses' => $controllers['emails'] . '@destroy', 'permissions' => 'emails-delete']);

        /**
         * Export Actions.
         */
        Route::get('csv', ['as' => 'admin.emails.csv', 'uses' => $controllers['emails'] . '@csv', 'permissions' => 'emails-export']);

        /**
         * Draft Actions.
         */
        Route::post('draft/{email?}', ['as' => 'admin.emails.draft', 'uses' => $controllers['emails'] . '@saveDraft', 'permissions' => 'emails-draft']);
        Route::put('publish/{email}', ['as' => 'admin.emails.publish', 'uses' => $controllers['emails'] . '@publishDraft', 'permissions' => 'emails-publish']);

        /**
         * Revision Actions.
         */
        Route::get('revision/{revision}', ['as' => 'admin.emails.revision', 'uses' => $controllers['emails'] . '@showRevision', 'permissions' => 'emails-edit']);

        /**
         * Duplicate Actions.
         */
        Route::post('duplicate/{email}', ['as' => 'admin.emails.duplicate', 'uses' => $controllers['emails'] . '@duplicate', 'permissions' => 'emails-duplicate']);

        /**
         * Preview Actions.
         */
        Route::post('preview/{email?}', ['as' => 'admin.emails.preview', 'uses' => $controllers['emails'] . '@preview', 'permissions' => 'emails-preview']);
    });
});
