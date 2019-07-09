<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'notifications' => '\\' . config('varbox.bindings.controllers.notifications_controller', \Varbox\Controllers\NotificationsController::class),
];

Route::group([
    'prefix' => 'admin',
    'middleware' => [
        'web',
        'varbox.auth.session:admin',
        'varbox.authenticated:admin',
        'varbox.check.roles',
        'varbox.check.permissions',
    ]
], function () use ($controllers) {
    Route::group([
        'prefix' => 'notifications',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.notifications.index', 'uses' => $controllers['notifications'] . '@index', 'permissions' => 'notifications-list']);
        Route::get('action/{notification?}', ['as' => 'admin.notifications.action', 'uses' => $controllers['notifications'] . '@actionNotification', 'permissions' => 'notifications-read']);
        Route::put('read/{notification}', ['as' => 'admin.notifications.mark_as_read', 'uses' => $controllers['notifications'] . '@markAsRead', 'permissions' => 'notifications-read']);
        Route::post('read-all', ['as' => 'admin.notifications.mark_all_as_read', 'uses' => $controllers['notifications'] . '@markAllAsRead', 'permissions' => 'notifications-read']);
        Route::delete('destroy/{notification}', ['as' => 'admin.notifications.destroy', 'uses' => $controllers['notifications'] . '@destroy', 'permissions' => 'notifications-delete']);
        Route::delete('delete-read', ['as' => 'admin.notifications.delete_read', 'uses' => $controllers['notifications'] . '@deleteOnlyRead', 'permissions' => 'notifications-delete']);
        Route::delete('delete-old', ['as' => 'admin.notifications.delete_old', 'uses' => $controllers['notifications'] . '@deleteOnlyOld', 'permissions' => 'notifications-delete']);
        Route::delete('delete-all', ['as' => 'admin.notifications.delete_all', 'uses' => $controllers['notifications'] . '@deleteAll', 'permissions' => 'notifications-delete']);
    });
});