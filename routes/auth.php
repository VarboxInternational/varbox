<?php

use Illuminate\Support\Facades\Route;

$controllers = [
    'login' => '\\' . config('varbox.varbox-binding.controllers.login_controller', \Varbox\Controllers\LoginController::class),
    'password_forgot' => '\\' . config('varbox.varbox-binding.controllers.password_forgot_controller', \Varbox\Controllers\ForgotPasswordController::class),
    'password_reset' => '\\' . config('varbox.varbox-binding.controllers.password_reset_controller', \Varbox\Controllers\ResetPasswordController::class),
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