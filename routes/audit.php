<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'notifications' => '\\' . config('varbox.varbox-binding.controllers.notifications_controller', \Varbox\Controllers\NotificationsController::class),
    'activity' => '\\' . config('varbox.varbox-binding.controllers.activity_controller', \Varbox\Controllers\ActivityController::class),
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
    /*
     * CRUD Notifications.
     */
    Route::group([
        'prefix' => 'notifications',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.notifications.index', 'uses' => $controllers['notifications'] . '@index', 'permissions' => 'notifications-list']);
        Route::get('action/{notification?}', ['as' => 'admin.notifications.action', 'uses' => $controllers['notifications'] . '@actionNotification']);
        Route::put('read/{notification}', ['as' => 'admin.notifications.mark_as_read', 'uses' => $controllers['notifications'] . '@markAsRead']);
        Route::post('read-all', ['as' => 'admin.notifications.mark_all_as_read', 'uses' => $controllers['notifications'] . '@markAllAsRead']);
        Route::delete('destroy/{notification}', ['as' => 'admin.notifications.destroy', 'uses' => $controllers['notifications'] . '@destroy', 'permissions' => 'notifications-delete']);
        Route::delete('delete-read', ['as' => 'admin.notifications.delete_read', 'uses' => $controllers['notifications'] . '@deleteOnlyRead', 'permissions' => 'notifications-delete']);
        Route::delete('delete-old', ['as' => 'admin.notifications.delete_old', 'uses' => $controllers['notifications'] . '@deleteOnlyOld', 'permissions' => 'notifications-delete']);
        Route::delete('delete-all', ['as' => 'admin.notifications.delete_all', 'uses' => $controllers['notifications'] . '@deleteAll', 'permissions' => 'notifications-delete']);
    });

    /**
     * CRUD Activity.
     */
    Route::group([
        'prefix' => 'activity',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.activity.index', 'uses' => $controllers['activity'] . '@index', 'permissions' => 'activity-list']);
        Route::delete('destroy/{activity}', ['as' => 'admin.activity.destroy', 'uses' => $controllers['activity'] . '@destroy', 'permissions' => 'activity-delete']);
        Route::delete('delete', ['as' => 'admin.activity.delete', 'uses' => $controllers['activity'] . '@delete', 'permissions' => 'activity-delete']);
        Route::delete('clean', ['as' => 'admin.activity.clean', 'uses' => $controllers['activity'] . '@clean', 'permissions' => 'activity-delete']);
    });
});