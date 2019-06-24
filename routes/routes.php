<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'dashboard' => '\\' . config('varbox.varbox-binding.controllers.dashboard_controller', \Varbox\Controllers\DashboardController::class),
    'login' => '\\' . config('varbox.varbox-binding.controllers.login_controller', \Varbox\Controllers\LoginController::class),
    'password_forgot' => '\\' . config('varbox.varbox-binding.controllers.password_forgot_controller', \Varbox\Controllers\ForgotPasswordController::class),
    'password_reset' => '\\' . config('varbox.varbox-binding.controllers.password_reset_controller', \Varbox\Controllers\ResetPasswordController::class),
    'users' => '\\' . config('varbox.varbox-binding.controllers.users_controller', \Varbox\Controllers\UsersController::class),
    'admins' => '\\' . config('varbox.varbox-binding.controllers.admins_controller', \Varbox\Controllers\AdminsController::class),
    'roles' => '\\' . config('varbox.varbox-binding.controllers.roles_controller', \Varbox\Controllers\RolesController::class),
    'permissions' => '\\' . config('varbox.varbox-binding.controllers.permissions_controller', \Varbox\Controllers\PermissionsController::class),
];

Route::group([
    'prefix' => 'admin',
    'middleware' => [
        'web',
        'varbox.auth.session:admin',
        'varbox.not.authenticated:admin',
    ]
], function () use ($controllers) {
    Route::get('login', ['as' => 'admin.login', 'uses' => $controllers['login'] . '@show']);
    Route::post('login', ['uses' => $controllers['login'] . '@login']);
    Route::post('logout', ['as' => 'admin.logout', 'uses' => $controllers['login'] . '@logout']);

    Route::get('forgot-password', ['as' => 'admin.password.forgot', 'uses' => $controllers['password_forgot'] . '@show']);
    Route::post('forgot-password', ['uses' => $controllers['password_forgot'] . '@sendResetLinkEmail']);

    Route::get('reset-password/{token}', ['as' => 'admin.password.reset', 'uses' => $controllers['password_reset'] . '@show']);
    Route::post('reset-password', ['as' => 'admin.password.update', 'uses' => $controllers['password_reset'] . '@reset']);
});

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
    Route::get('/', ['as' => 'admin', 'uses' => $controllers['dashboard'] . '@index']);

    /*
     * Users.
     */
    Route::group([
        'prefix' => 'users',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.users.index', 'uses' => $controllers['users'] . '@index', 'permissions' => 'users-list']);
        Route::get('create', ['as' => 'admin.users.create', 'uses' => $controllers['users'] . '@create', 'permissions' => 'users-add']);
        Route::post('store', ['as' => 'admin.users.store', 'uses' => $controllers['users'] . '@store', 'permissions' => 'users-add']);
        Route::get('edit/{user}', ['as' => 'admin.users.edit', 'uses' => $controllers['users'] . '@edit', 'permissions' => 'users-edit']);
        Route::put('update/{user}', ['as' => 'admin.users.update', 'uses' => $controllers['users'] . '@update', 'permissions' => 'users-edit']);
        Route::delete('destroy/{user}', ['as' => 'admin.users.destroy', 'uses' => $controllers['users'] . '@destroy', 'permissions' => 'users-delete']);
        Route::post('impersonate/{user}', ['as' => 'admin.users.impersonate', 'uses' => $controllers['users'] . '@impersonate', 'permissions' => 'users-impersonate']);
    });

    /*
     * Admins.
     */
    Route::group([
        'prefix' => 'admins',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.admins.index', 'uses' => $controllers['admins'] . '@index', 'permissions' => 'admins-list']);
        Route::get('create', ['as' => 'admin.admins.create', 'uses' => $controllers['admins'] . '@create', 'permissions' => 'admins-add']);
        Route::post('store', ['as' => 'admin.admins.store', 'uses' => $controllers['admins'] . '@store', 'permissions' => 'admins-add']);
        Route::get('edit/{user}', ['as' => 'admin.admins.edit', 'uses' => $controllers['admins'] . '@edit', 'permissions' => 'admins-edit']);
        Route::put('update/{user}', ['as' => 'admin.admins.update', 'uses' => $controllers['admins'] . '@update', 'permissions' => 'admins-edit']);
        Route::delete('destroy/{user}', ['as' => 'admin.admins.destroy', 'uses' => $controllers['admins'] . '@destroy', 'permissions' => 'admins-delete']);
    });

    /*
     * Roles.
     */
    Route::group([
        'prefix' => 'roles',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.roles.index', 'uses' => $controllers['roles'] . '@index', 'permissions' => 'roles-list']);
        Route::get('create', ['as' => 'admin.roles.create', 'uses' => $controllers['roles'] . '@create', 'permissions' => 'roles-add']);
        Route::post('store', ['as' => 'admin.roles.store', 'uses' => $controllers['roles'] . '@store', 'permissions' => 'roles-add']);
        Route::get('edit/{role}', ['as' => 'admin.roles.edit', 'uses' => $controllers['roles'] . '@edit', 'permissions' => 'roles-edit']);
        Route::put('update/{role}', ['as' => 'admin.roles.update', 'uses' => $controllers['roles'] . '@update', 'permissions' => 'roles-edit']);
        Route::delete('destroy/{role}', ['as' => 'admin.roles.destroy', 'uses' => $controllers['roles'] . '@destroy', 'permissions' => 'roles-delete']);
    });

    /**
     * Permissions.
     */
    Route::group([
        'prefix' => 'permissions',
    ], function () use ($controllers) {
        Route::get('/', ['as' => 'admin.permissions.index', 'uses' => $controllers['permissions'] . '@index', 'permissions' => 'permissions-list']);
        Route::get('create', ['as' => 'admin.permissions.create', 'uses' => $controllers['permissions'] . '@create', 'permissions' => 'permissions-add']);
        Route::post('store', ['as' => 'admin.permissions.store', 'uses' => $controllers['permissions'] . '@store', 'permissions' => 'permissions-add']);
        Route::get('edit/{permission}', ['as' => 'admin.permissions.edit', 'uses' => $controllers['permissions'] . '@edit', 'permissions' => 'permissions-edit']);
        Route::put('update/{permission}', ['as' => 'admin.permissions.update', 'uses' => $controllers['permissions'] . '@update', 'permissions' => 'permissions-edit']);
        Route::delete('destroy/{permission}', ['as' => 'admin.permissions.destroy', 'uses' => $controllers['permissions'] . '@destroy', 'permissions' => 'permissions-delete']);
    });
});