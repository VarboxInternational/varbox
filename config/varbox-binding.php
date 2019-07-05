<?php

/*
| ------------------------------------------------------------------------------------------------------------------
| Class Bindings
| ------------------------------------------------------------------------------------------------------------------
|
| FQNs of the classes used by the Varbox platform internally to achieve different functionalities.
| Each of these classes represents a concrete implementation that is bound to the Laravel's IoC container.
|
| If you need to extend or modify a functionality, you can swap the implementation below with your own class.
| Swapping the implementation, requires some steps, like extending the core class, or implementing an interface.
|
*/
return [

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Service Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'services' => [

        /*
        |
        | Concrete implementation for the "query cache service".
        | To extend or replace this functionality, change the value below with your full "query cache service" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Services\QueryCacheService" class
        | - or at least implement the "Varbox\Contracts\QueryCacheServiceContract" interface
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - query_cache() OR app('query_cache.service') OR app('\Varbox\Contracts\QueryCacheServiceContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'query_cache_service' => \Varbox\Services\QueryCacheService::class,

    ],

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Model Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'models' => [

        /*
        |
        | Concrete implementation for the "user model".
        | To extend or replace this functionality, change the value below with your full "user model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\User" class
        | - or at least implement the "Varbox\Contracts\UserModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('user.model') OR app('\Varbox\Contracts\UserModelContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'user_model' => \Varbox\Models\User::class,

        /*
        |
        | Concrete implementation for the "role model".
        | To extend or replace this functionality, change the value below with your full "role model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Role" class
        | - or at least implement the "Varbox\Contracts\RoleModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('role.model') OR app('\Varbox\Contracts\RoleModelContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'role_model' => \Varbox\Models\Role::class,

        /*
        |
        | Concrete implementation for the "permission model".
        | To extend or replace this functionality, change the value below with your full "permission model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Permission" class
        | - or at least implement the "Varbox\Contracts\PermissionModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('permission.model') OR app('\Varbox\Contracts\PermissionModelContract')
        | - or you could even use your own class as a direct implementation
        */
        'permission_model' => \Varbox\Models\Permission::class,

        /*
        |
        | Concrete implementation for the "activity model".
        | To extend or replace this functionality, change the value below with your full "activity model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Activity" class
        | - or at least implement the "Varbox\Contracts\ActivityModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('activity.model') OR app('\Varbox\Contracts\ActivityModelContract')
        | - or you could even use your own class as a direct implementation
        */
        'activity_model' => \Varbox\Models\Activity::class,

        /*
        |
        | Concrete implementation for the "country model".
        | To extend or replace this functionality, change the value below with your full "country model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Country" class
        | - or at least implement the "Varbox\Contracts\CountryModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('country.model') OR app('\Varbox\Contracts\CountryModelContract')
        | - or you could even use your own class as a direct implementation
        */
        'country_model' => \Varbox\Models\Country::class,

        /*
        |
        | Concrete implementation for the "state model".
        | To extend or replace this functionality, change the value below with your full "state model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\State" class
        | - or at least implement the "Varbox\Contracts\StateModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('state.model') OR app('\Varbox\Contracts\StateModelContract')
        | - or you could even use your own class as a direct implementation
        */
        'state_model' => \Varbox\Models\State::class,

        /*
        |
        | Concrete implementation for the "city model".
        | To extend or replace this functionality, change the value below with your full "city model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\City" class
        | - or at least implement the "Varbox\Contracts\CityModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('city.model') OR app('\Varbox\Contracts\CityModelContract')
        | - or you could even use your own class as a direct implementation
        */
        'city_model' => \Varbox\Models\City::class,

        /*
        |
        | Concrete implementation for the "address model".
        | To extend or replace this functionality, change the value below with your full "address model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Address" class
        | - or at least implement the "Varbox\Contracts\AddressModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('address.model') OR app('\Varbox\Contracts\AddressModelContract')
        | - or you could even use your own class as a direct implementation
        */
        'address_model' => \Varbox\Models\Address::class,

        /*
        |
        | Concrete implementation for the "config model".
        | To extend or replace this functionality, change the value below with your full "config model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Config" class
        | - or at least implement the "Varbox\Contracts\ConfigModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('config.model') OR app('\Varbox\Contracts\ConfigModelContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'config_model' => \Varbox\Models\Config::class,

        /*
        |
        | Concrete implementation for the "backup model".
        | To extend or replace this functionality, change the value below with your full "backup model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Backup" class
        | - or at least implement the "Varbox\Contracts\BackupModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('backup.model') OR app('\Varbox\Contracts\BackupModelContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'backup_model' => \Varbox\Models\Backup::class,

        /*
        |
        | Concrete implementation for the "error model".
        | To extend or replace this functionality, change the value below with your full "error model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Error" class
        | - or at least implement the "Varbox\Contracts\ErrorModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('error.model') OR app('\Varbox\Contracts\ErrorModelContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'error_model' => \Varbox\Models\Error::class,

    ],

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Controller Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'controllers' => [

        /*
        |
        | Concrete implementation for the "dashboard controller".
        | To extend or replace this functionality, change the value below with your full "dashboard controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\DashboardController" class
        | - or you'll have to implement the following public methods yourself: show(), getAuthenticateOptions()
        |
        */
        'dashboard_controller' => \Varbox\Controllers\DashboardController::class,

        /*
        |
        | Concrete implementation for the "login controller".
        | To extend or replace this functionality, change the value below with your full "login controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\LoginController" class
        | - or you'll have to implement the following public methods yourself: show(), getAuthenticateOptions()
        |
        */
        'login_controller' => \Varbox\Controllers\LoginController::class,

        /*
        |
        | Concrete implementation for the "password forgot controller".
        | To extend or replace this functionality, change the value below with your full "password forgot controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\ForgotPasswordController" class
        | - or you'll have to implement the following public methods yourself: show(), sendResetLinkEmail(), getResetPasswordOptions()
        |
        */
        'password_forgot_controller' => \Varbox\Controllers\ForgotPasswordController::class,

        /*
        |
        | Concrete implementation for the "password reset controller".
        | To extend or replace this functionality, change the value below with your full "password reset controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\ResetPasswordController" class
        | - or you'll have to implement the following public methods yourself: show(), reset(), getResetPasswordOptions()
        |
        */
        'password_reset_controller' => \Varbox\Controllers\ResetPasswordController::class,

        /*
        |
        | Concrete implementation for the "users controller".
        | To extend or replace this functionality, change the value below with your full "users controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\UsersController" class
        | - or you'll have to implement the following public methods yourself: index(), create(), store(), edit(), update(), destroy(), impersonate()
        |
        */
        'users_controller' => \Varbox\Controllers\UsersController::class,

        /*
        |
        | Concrete implementation for the "admins controller".
        | To extend or replace this functionality, change the value below with your full "admins controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\AdminsController" class
        | - or you'll have to implement the following public methods yourself: index(), create(), store(), edit(), update(), destroy()
        |
        */
        'admins_controller' => \Varbox\Controllers\AdminsController::class,

        /*
        |
        | Concrete implementation for the "roles controller".
        | To extend or replace this functionality, change the value below with your full "roles controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\RolesController" class
        | - or you'll have to implement the following public methods yourself: index(), create(), store(), edit(), update(), destroy()
        |
        */
        'roles_controller' => \Varbox\Controllers\RolesController::class,

        /*
        |
        | Concrete implementation for the "permissions controller".
        | To extend or replace this functionality, change the value below with your full "permissions controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\PermissionsController" class
        | - or you'll have to implement the following public methods yourself: index(), create(), store(), edit(), update(), destroy()
        |
        */
        'permissions_controller' => \Varbox\Controllers\PermissionsController::class,

        /*
        |
        | Concrete implementation for the "notifications controller".
        | To extend or replace this functionality, change the value below with your full "notifications controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\NotificationsController" class
        | - or you'll have to implement the following public methods yourself: index(), destroy(), actionNotification(), markAsRead(), markAllAsRead(), deleteRead(), deleteOld(), deleteAll()
        |
        */
        'notifications_controller' => \Varbox\Controllers\NotificationsController::class,

        /*
        |
        | Concrete implementation for the "activity controller".
        | To extend or replace this functionality, change the value below with your full "activity controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\ActivityController" class
        | - or you'll have to implement the following public methods yourself: index(), destroy(), clean(), delete()
        |
        */
        'activity_controller' => \Varbox\Controllers\ActivityController::class,

        /*
        |
        | Concrete implementation for the "countries controller".
        | To extend or replace this functionality, change the value below with your full "countries controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\CountriesController" class
        | - or you'll have to implement the following public methods yourself: index(), create(), store(), edit(), update(), destroy()
        |
        */
        'countries_controller' => \Varbox\Controllers\CountriesController::class,

        /*
        |
        | Concrete implementation for the "states controller".
        | To extend or replace this functionality, change the value below with your full "states controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\StatesController" class
        | - or you'll have to implement the following public methods yourself: index(), create(), store(), edit(), update(), destroy(), get()
        |
        */
        'states_controller' => \Varbox\Controllers\StatesController::class,

        /*
        |
        | Concrete implementation for the "cities controller".
        | To extend or replace this functionality, change the value below with your full "cities controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\CitiesController" class
        | - or you'll have to implement the following public methods yourself: index(), create(), store(), edit(), update(), destroy(), get()
        |
        */
        'cities_controller' => \Varbox\Controllers\CitiesController::class,

        /*
        |
        | Concrete implementation for the "addresses controller".
        | To extend or replace this functionality, change the value below with your full "addresses controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\AddressesController" class
        | - or you'll have to implement the following public methods yourself: index(), create(), store(), edit(), update(), destroy()
        |
        */
        'addresses_controller' => \Varbox\Controllers\AddressesController::class,

        /*
        |
        | Concrete implementation for the "configs controller".
        | To extend or replace this functionality, change the value below with your full "configs controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\ConfigsController" class
        | - or you'll have to implement the following public methods yourself: index(), create(), store(), edit(), update(), destroy()
        |
        */
        'configs_controller' => \Varbox\Controllers\ConfigsController::class,

        /*
        |
        | Concrete implementation for the "backups controller".
        | To extend or replace this functionality, change the value below with your full "backups controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\BackupsController" class
        | - or you'll have to implement the following public methods yourself: index(), create(), destroy(), download(), clear()
        |
        */
        'backups_controller' => \Varbox\Controllers\BackupsController::class,

        /*
        |
        | Concrete implementation for the "errors controller".
        | To extend or replace this functionality, change the value below with your full "errors controller" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Controllers\ErrorsController" class
        | - or you'll have to implement the following public methods yourself: index(), show(), destroy(), clear()
        |
        */
        'errors_controller' => \Varbox\Controllers\ErrorsController::class,

    ],

    'form_requests' => [

        /*
        |
        | Concrete implementation for the "user form request".
        | To extend or replace this functionality, change the value below with your full "user form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\UserRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'user_form_request' => \Varbox\Requests\UserRequest::class,

        /*
        |
        | Concrete implementation for the "admin form request".
        | To extend or replace this functionality, change the value below with your full "admin form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\AdminRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'admin_form_request' => \Varbox\Requests\AdminRequest::class,

        /*
        |
        | Concrete implementation for the "role form request".
        | To extend or replace this functionality, change the value below with your full "role form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\RoleRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'role_form_request' => \Varbox\Requests\RoleRequest::class,

        /*
        |
        | Concrete implementation for the "permission form request".
        | To extend or replace this functionality, change the value below with your full "permission form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\PermissionRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'permission_form_request' => \Varbox\Requests\PermissionRequest::class,

        /*
        |
        | Concrete implementation for the "login form request".
        | To extend or replace this functionality, change the value below with your full "login form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\LoginRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'login_form_request' => \Varbox\Requests\LoginRequest::class,

        /*
        |
        | Concrete implementation for the "password forgot form request".
        | To extend or replace this functionality, change the value below with your full "password forgot form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\PasswordForgotRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'password_forgot_form_request' => \Varbox\Requests\PasswordForgotRequest::class,

        /*
        |
        | Concrete implementation for the "password reset form request".
        | To extend or replace this functionality, change the value below with your full "password reset form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\PasswordResetRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'password_reset_form_request' => \Varbox\Requests\PasswordResetRequest::class,

        /*
        |
        | Concrete implementation for the "country form request".
        | To extend or replace this functionality, change the value below with your full "country form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\CountryRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'country_form_request' => \Varbox\Requests\CountryRequest::class,

        /*
        |
        | Concrete implementation for the "state form request".
        | To extend or replace this functionality, change the value below with your full "state form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\StateRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'state_form_request' => \Varbox\Requests\StateRequest::class,

        /*
        |
        | Concrete implementation for the "city form request".
        | To extend or replace this functionality, change the value below with your full "city form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\CityRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'city_form_request' => \Varbox\Requests\CityRequest::class,

        /*
        |
        | Concrete implementation for the "address form request".
        | To extend or replace this functionality, change the value below with your full "address form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\AddressRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'address_form_request' => \Varbox\Requests\AddressRequest::class,

        /*
        |
        | Concrete implementation for the "config form request".
        | To extend or replace this functionality, change the value below with your full "config form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\ConfigRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'config_form_request' => \Varbox\Requests\ConfigRequest::class,

    ],

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Helper Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'helpers' => [

        /*
        |
        | Concrete implementation for the "admin form helper".
        | To extend or replace this functionality, change the value below with your full "admin form helper" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Helpers\AdminFormHelper" class
        | - or at least implement the "Varbox\Contracts\AdminFormHelperContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - form_admin() OR app('admin_form.helper') OR app('\Varbox\Contracts\AdminFormHelperContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'admin_form_helper' => \Varbox\Helpers\AdminFormHelper::class,

        /*
        |
        | Concrete implementation for the "admin menu helper".
        | To extend or replace this functionality, change the value below with your full "admin menu helper" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Helpers\AdminMenuHelper" class
        | - or at least implement the "Varbox\Contracts\AdminMenuHelperContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - menu_admin() OR app('admin_menu.helper') OR app('\Varbox\Contracts\AdminMenuHelperContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'admin_menu_helper' => \Varbox\Helpers\AdminMenuHelper::class,

        /*
        |
        | Concrete implementation for the "flash helper".
        | To extend or replace this functionality, change the value below with your full "flash helper" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Helpers\FlashHelper" class
        | - or at least implement the "Varbox\Contracts\FlashHelperContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - flash() OR app('flash.helper') OR app('\Varbox\Contracts\FlashHelperContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'flash_helper' => \Varbox\Helpers\FlashHelper::class,

        /*
        |
        | Concrete implementation for the "meta helper".
        | To extend or replace this functionality, change the value below with your full "meta helper" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Helpers\MetaHelper" class
        | - or at least implement the "Varbox\Contracts\MetaHelperContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - meta() OR app('meta.helper') OR app('\Varbox\Contracts\MetaHelperContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'meta_helper' => \Varbox\Helpers\MetaHelper::class,

        /*
        |
        | Concrete implementation for the "validation helper".
        | To extend or replace this functionality, change the value below with your full "validation helper" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Helpers\ValidationHelper" class
        | - or at least implement the "Varbox\Contracts\ValidationHelperContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - validation() OR app('validation.helper') OR app('\Varbox\Contracts\ValidationHelperContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'validation_helper' => \Varbox\Helpers\ValidationHelper::class,

        /*
        |
        | Concrete implementation for the "pagination helper".
        | To extend or replace this functionality, change the value below with your full "pagination helper" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Helpers\PaginationHelper" class
        | - or at least implement the "Varbox\Contracts\PaginationHelperContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - pagination() OR app('pagination.helper') OR app('\Varbox\Contracts\PaginationHelperContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'pagination_helper' => \Varbox\Helpers\PaginationHelper::class,

        /*
        |
        | Concrete implementation for the "button helper".
        | To extend or replace this functionality, change the value below with your full "button helper" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Helpers\ButtonHelper" class
        | - or at least implement the "Varbox\Contracts\ButtonHelperContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - button() OR app('button.helper') OR app('\Varbox\Contracts\ButtonHelperContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'button_helper' => \Varbox\Helpers\ButtonHelper::class,

    ],

    /*
    | --------------------------------------------------------------------------------------------------------------
    | View Composers Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'view_composers' => [

        /*
        |
        | Concrete implementation for the "admin menu view composer".
        | To extend or replace this functionality, change the value below with your full "admin menu view composer" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Composers\AdminMenuComposer" class
        | - or at least implement the following methods: compose()
        |
        */
        'admin_menu_view_composer' => \Varbox\Composers\AdminMenuComposer::class,

        /*
        |
        | Concrete implementation for the "notifications view composer".
        | To extend or replace this functionality, change the value below with your full "notifications view composer" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Composers\NotificationsComposer" class
        | - or at least implement the following methods: compose()
        |
        */
        'notifications_view_composer' => \Varbox\Composers\NotificationsComposer::class,

    ],

];
