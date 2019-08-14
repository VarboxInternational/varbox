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
        | Concrete implementation for the "upload service".
        | To extend or replace this functionality, change the value below with your full "upload service" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Services\UploadService" class
        | - or at least implement the "Varbox\Contracts\UploadServiceContract" interface
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - upload() OR app('upload.service') OR app('\Varbox\Contracts\UploadServiceContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'upload_service' => \Varbox\Services\UploadService::class,

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
        | Concrete implementation for the "upload model".
        | To extend or replace this functionality, change the value below with your full "upload model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Upload" class
        | - or at least implement the "Varbox\Contracts\UploadModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('upload.model') OR app('\Varbox\Contracts\UploadModelContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'upload_model' => \Varbox\Models\Upload::class,

        /*
        |
        | Concrete implementation for the "revision model".
        | To extend or replace this functionality, change the value below with your full "revision model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Revision" class
        | - or at least implement the "Varbox\Contracts\RevisionModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('revision.model') OR app('\Varbox\Contracts\RevisionModelContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'revision_model' => \Varbox\Models\Revision::class,

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

        /*
        |
        | Concrete implementation for the "email model".
        | To extend or replace this functionality, change the value below with your full "email model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Email" class
        | - or at least implement the "Varbox\Contracts\EmailModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('email.model') OR app('\Varbox\Contracts\EmailModelContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'email_model' => \Varbox\Models\Email::class,

        /*
        |
        | Concrete implementation for the "block model".
        | To extend or replace this functionality, change the value below with your full "block model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Block" class
        | - or at least implement the "Varbox\Contracts\BlockModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('block.model') OR app('\Varbox\Contracts\BlockModelContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'block_model' => \Varbox\Models\Block::class,

        /*
        |
        | Concrete implementation for the "page model".
        | To extend or replace this functionality, change the value below with your full "page model" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Models\Page" class
        | - or at least implement the "Varbox\Contracts\PageModelContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - app('page.model') OR app('\Varbox\Contracts\PageModelContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'page_model' => \Varbox\Models\Page::class,

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
        | Your class will have to:
        | - extend the "Varbox\Controllers\DashboardController" class
        |
        */
        'dashboard_controller' => \Varbox\Controllers\DashboardController::class,

        /*
        |
        | Concrete implementation for the "login controller".
        | To extend or replace this functionality, change the value below with your full "login controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\LoginController" class
        |
        */
        'login_controller' => \Varbox\Controllers\LoginController::class,

        /*
        |
        | Concrete implementation for the "password forgot controller".
        | To extend or replace this functionality, change the value below with your full "password forgot controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\ForgotPasswordController" class
        |
        */
        'password_forgot_controller' => \Varbox\Controllers\ForgotPasswordController::class,

        /*
        |
        | Concrete implementation for the "password reset controller".
        | To extend or replace this functionality, change the value below with your full "password reset controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\ResetPasswordController" class
        |
        */
        'password_reset_controller' => \Varbox\Controllers\ResetPasswordController::class,

        /*
        |
        | Concrete implementation for the "users controller".
        | To extend or replace this functionality, change the value below with your full "users controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\UsersController" class
        |
        */
        'users_controller' => \Varbox\Controllers\UsersController::class,

        /*
        |
        | Concrete implementation for the "admins controller".
        | To extend or replace this functionality, change the value below with your full "admins controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\AdminsController" class
        |
        */
        'admins_controller' => \Varbox\Controllers\AdminsController::class,

        /*
        |
        | Concrete implementation for the "roles controller".
        | To extend or replace this functionality, change the value below with your full "roles controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\RolesController" class
        |
        */
        'roles_controller' => \Varbox\Controllers\RolesController::class,

        /*
        |
        | Concrete implementation for the "permissions controller".
        | To extend or replace this functionality, change the value below with your full "permissions controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\PermissionsController" class
        |
        */
        'permissions_controller' => \Varbox\Controllers\PermissionsController::class,

        /*
        |
        | Concrete implementation for the "upload controller".
        | To extend or replace this functionality, change the value below with your full "upload controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\UploadController" class
        |
        */
        'upload_controller' => \Varbox\Controllers\UploadController::class,

        /*
        |
        | Concrete implementation for the "uploads controller".
        | To extend or replace this functionality, change the value below with your full "uploads controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\UploadsController" class
        |
        */
        'uploads_controller' => \Varbox\Controllers\UploadsController::class,

        /*
        |
        | Concrete implementation for the "revisions controller".
        | To extend or replace this functionality, change the value below with your full "revisions controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\RevisionsController" class
        |
        */
        'revisions_controller' => \Varbox\Controllers\RevisionsController::class,

        /*
        |
        | Concrete implementation for the "notifications controller".
        | To extend or replace this functionality, change the value below with your full "notifications controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\NotificationsController" class
        |
        */
        'notifications_controller' => \Varbox\Controllers\NotificationsController::class,

        /*
        |
        | Concrete implementation for the "activity controller".
        | To extend or replace this functionality, change the value below with your full "activity controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\ActivityController" class
        |
        */
        'activity_controller' => \Varbox\Controllers\ActivityController::class,

        /*
        |
        | Concrete implementation for the "countries controller".
        | To extend or replace this functionality, change the value below with your full "countries controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\CountriesController" class
        |
        */
        'countries_controller' => \Varbox\Controllers\CountriesController::class,

        /*
        |
        | Concrete implementation for the "states controller".
        | To extend or replace this functionality, change the value below with your full "states controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\StatesController" class
        |
        */
        'states_controller' => \Varbox\Controllers\StatesController::class,

        /*
        |
        | Concrete implementation for the "cities controller".
        | To extend or replace this functionality, change the value below with your full "cities controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\CitiesController" class
        |
        */
        'cities_controller' => \Varbox\Controllers\CitiesController::class,

        /*
        |
        | Concrete implementation for the "addresses controller".
        | To extend or replace this functionality, change the value below with your full "addresses controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\AddressesController" class
        |
        */
        'addresses_controller' => \Varbox\Controllers\AddressesController::class,

        /*
        |
        | Concrete implementation for the "configs controller".
        | To extend or replace this functionality, change the value below with your full "configs controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\ConfigsController" class
        |
        */
        'configs_controller' => \Varbox\Controllers\ConfigsController::class,

        /*
        |
        | Concrete implementation for the "backups controller".
        | To extend or replace this functionality, change the value below with your full "backups controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\BackupsController" class
        |
        */
        'backups_controller' => \Varbox\Controllers\BackupsController::class,

        /*
        |
        | Concrete implementation for the "errors controller".
        | To extend or replace this functionality, change the value below with your full "errors controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\ErrorsController" class
        |
        */
        'errors_controller' => \Varbox\Controllers\ErrorsController::class,

        /*
        |
        | Concrete implementation for the "froala controller".
        | To extend or replace this functionality, change the value below with your full "froala controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\FroalaController" class
        |
        */
        'froala_controller' => \Varbox\Controllers\FroalaController::class,

        /*
        |
        | Concrete implementation for the "emails controller".
        | To extend or replace this functionality, change the value below with your full "emails controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\EmailsController" class
        |
        */
        'emails_controller' => \Varbox\Controllers\EmailsController::class,

        /*
        |
        | Concrete implementation for the "blocks controller".
        | To extend or replace this functionality, change the value below with your full "blocks controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\BlocksController" class
        |
        */
        'blocks_controller' => \Varbox\Controllers\BlocksController::class,

        /*
        |
        | Concrete implementation for the "pages controller".
        | To extend or replace this functionality, change the value below with your full "pages controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\PagesController" class
        |
        */
        'pages_controller' => \Varbox\Controllers\PagesController::class,

        /*
        |
        | Concrete implementation for the "pages tree controller".
        | To extend or replace this functionality, change the value below with your full "pages tree controller" FQN.
        |
        | Your class will have to:
        | - extend the "Varbox\Controllers\Pages\TreeController" class
        |
        */
        'pages_tree_controller' => \Varbox\Controllers\Pages\TreeController::class,

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
        | Concrete implementation for the "upload form request".
        | To extend or replace this functionality, change the value below with your full "upload form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\UploadRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'upload_form_request' => \Varbox\Requests\UploadRequest::class,

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

        /*
        |
        | Concrete implementation for the "email form request".
        | To extend or replace this functionality, change the value below with your full "email form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\EmailRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'email_form_request' => \Varbox\Requests\EmailRequest::class,

        /*
        |
        | Concrete implementation for the "block form request".
        | To extend or replace this functionality, change the value below with your full "block form request" FQN.
        |
        | Your class will have to (firs options is recommended):
        | - extend the "\Varbox\Requests\BlockRequest" class
        | - or extend the "\Illuminate\Foundation\Http\FormRequest" class.
        |
        */
        'block_form_request' => \Varbox\Requests\BlockRequest::class,

    ],

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Middleware Class Bindings
    | --------------------------------------------------------------------------------------------------------------
    */
    'middleware' => [

        /*
        |
        | Concrete implementation for the "authenticate session middleware".
        | To extend or replace this functionality, change the value below with your full "authenticate session middleware" FQN.
        |
        | Once the value below is changed, your new middleware will be automatically registered with the application.
        |
        | You can then use the middleware by its alias: "varbox.auth.session"
        |
        */
        'authenticate_session_middleware' => \Varbox\Middleware\AuthenticateSession::class,

        /*
        |
        | Concrete implementation for the "authenticated middleware".
        | To extend or replace this functionality, change the value below with your full "authenticated middleware" FQN.
        |
        | Once the value below is changed, your new middleware will be automatically registered with the application.
        |
        | You can then use the middleware by its alias: "varbox.authenticated"
        |
        */
        'authenticated_middleware' => \Varbox\Middleware\Authenticated::class,

        /*
        |
        | Concrete implementation for the "not authenticated middleware".
        | To extend or replace this functionality, change the value below with your full "not authenticated middleware" FQN.
        |
        | Once the value below is changed, your new middleware will be automatically registered with the application.
        |
        | You can then use the middleware by its alias: "varbox.not.authenticated"
        |
        */
        'not_authenticated_middleware' => \Varbox\Middleware\NotAuthenticated::class,

        /*
        |
        | Concrete implementation for the "check roles middleware".
        | To extend or replace this functionality, change the value below with your full "check roles middleware" FQN.
        |
        | Once the value below is changed, your new middleware will be automatically registered with the application.
        |
        | You can then use the middleware by its alias: "varbox.check.roles"
        |
        */
        'check_roles_middleware' => \Varbox\Middleware\CheckRoles::class,

        /*
        |
        | Concrete implementation for the "check permissions middleware".
        | To extend or replace this functionality, change the value below with your full "check permissions middleware" FQN.
        |
        | Once the value below is changed, your new middleware will be automatically registered with the application.
        |
        | You can then use the middleware by its alias: "varbox.check.permissions"
        |
        */
        'check_permissions_middleware' => \Varbox\Middleware\CheckPermissions::class,

        /*
        |
        | Concrete implementation for the "override configs middleware".
        | To extend or replace this functionality, change the value below with your full "override configs middleware" FQN.
        |
        | Once the value below is changed, your new middleware will be automatically registered with the application.
        |
        | You can then use the middleware by its alias: "varbox.override.configs"
        |
        */
        'override_configs_middleware' => \Varbox\Middleware\OverrideConfigs::class,

        /*
        |
        | Concrete implementation for the "optimize images middleware".
        | To extend or replace this functionality, change the value below with your full "optimize images middleware" FQN.
        |
        | Once the value below is changed, your new middleware will be automatically registered with the application.
        |
        | You can then use the middleware by its alias: "varbox.optimize.images"
        |
        */
        'optimize_images_middleware' => \Varbox\Middleware\OptimizeImages::class,

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

        /*
        |
        | Concrete implementation for the "uploaded helper".
        | To extend or replace this functionality, change the value below with your full "uploaded helper" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Helpers\UploadedHelper" class
        | - or at least implement the "Varbox\Contracts\UploadedHelperContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - uploaded() OR app('uploaded.helper') OR app('\Varbox\Contracts\UploadedHelperContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'uploaded_helper' => \Varbox\Helpers\UploadedHelper::class,

        /*
        |
        | Concrete implementation for the "uploader helper".
        | To extend or replace this functionality, change the value below with your full "uploader helper" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Helpers\UploaderHelper" class
        | - or at least implement the "Varbox\Contracts\UploaderHelperContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - uploader() OR app('uploader.helper') OR app('\Varbox\Contracts\UploaderHelperContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'uploader_helper' => \Varbox\Helpers\UploaderHelper::class,

        /*
        |
        | Concrete implementation for the "draft helper".
        | To extend or replace this functionality, change the value below with your full "draft helper" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Helpers\RevisionHelper" class
        | - or at least implement the "Varbox\Contracts\RevisionHelperContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - draft() OR app('draft.helper') OR app('\Varbox\Contracts\DraftHelperContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'draft_helper' => \Varbox\Helpers\DraftHelper::class,

        /*
        |
        | Concrete implementation for the "revision helper".
        | To extend or replace this functionality, change the value below with your full "revision helper" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Helpers\RevisionHelper" class
        | - or at least implement the "Varbox\Contracts\RevisionHelperContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - revision() OR app('revision.helper') OR app('\Varbox\Contracts\RevisionHelperContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'revision_helper' => \Varbox\Helpers\RevisionHelper::class,

        /*
        |
        | Concrete implementation for the "block helper".
        | To extend or replace this functionality, change the value below with your full "block helper" FQN.
        |
        | Your class will have to (first option is recommended):
        | - extend the "Varbox\Helpers\BlockHelper" class
        | - or at least implement the "Varbox\Contracts\BlockHelperContract" interface.
        |
        | Regardless of the concrete implementation below, you can still use it like:
        | - block() OR app('block.helper') OR app('\Varbox\Contracts\BlockHelperContract')
        | - or you could even use your own class as a direct implementation
        |
        */
        'block_helper' => \Varbox\Helpers\BlockHelper::class,

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
