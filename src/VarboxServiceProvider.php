<?php

namespace Varbox;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Varbox\Commands\ActivityCleanCommand;
use Varbox\Commands\NotificationsCleanCommand;
use Varbox\Commands\InstallCommand;
use Varbox\Composers\AdminMenuComposer;
use Varbox\Composers\NotificationsComposer;
use Varbox\Contracts\ActivityModelContract;
use Varbox\Contracts\AdminFormHelperContract;
use Varbox\Contracts\AdminMenuHelperContract;
use Varbox\Contracts\ButtonHelperContract;
use Varbox\Contracts\FlashHelperContract;
use Varbox\Contracts\MetaHelperContract;
use Varbox\Contracts\PaginationHelperContract;
use Varbox\Contracts\PermissionModelContract;
use Varbox\Contracts\QueryCacheServiceContract;
use Varbox\Contracts\RoleModelContract;
use Varbox\Contracts\UserModelContract;
use Varbox\Contracts\ValidationHelperContract;
use Varbox\Facades\VarboxFacade;
use Varbox\Helpers\AdminFormHelper;
use Varbox\Helpers\AdminMenuHelper;
use Varbox\Helpers\ButtonHelper;
use Varbox\Helpers\FlashHelper;
use Varbox\Helpers\MetaHelper;
use Varbox\Helpers\PaginationHelper;
use Varbox\Helpers\ValidationHelper;
use Varbox\Middleware\AuthenticateSession;
use Varbox\Middleware\Authenticated;
use Varbox\Middleware\CheckPermissions;
use Varbox\Middleware\CheckRoles;
use Varbox\Middleware\NotAuthenticated;
use Varbox\Models\Activity;
use Varbox\Models\Permission;
use Varbox\Models\Role;
use Varbox\Models\User;
use Varbox\Services\QueryCacheService;

class VarboxServiceProvider extends BaseServiceProvider
{
    /**
     * @var ConfigRepository
     */
    protected $config;

    /**
     * @var Router
     */
    protected $router;

    /**
     * Create a new service provider instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);

        $this->config = $this->app->config;
    }

    /**
     * Bootstrap the application services.
     *
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $this->router = $router;

        $this->publishConfigs();
        $this->overrideConfigs();
        $this->publishMigrations();
        $this->publishViews();
        $this->publishAssets();
        $this->registerCommands();
        $this->registerMiddlewares();
        $this->registerViewComposers();
        $this->registerRouteBindings();
        $this->loadRoutes();
        $this->loadBreadcrumbs();
        $this->registerBladeDirectives();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigs();
        $this->registerFacades();
        $this->registerServiceBindings();
        $this->registerModelBindings();
        $this->registerHelperBindings();
    }

    /**
     * @return void
     */
    protected function publishConfigs()
    {
        $this->publishes([
            __DIR__ . '/../config/varbox-activity.php' => config_path('varbox/varbox-activity.php'),
            __DIR__ . '/../config/varbox-binding.php' => config_path('varbox/varbox-binding.php'),
            __DIR__ . '/../config/varbox-cache.php' => config_path('varbox/varbox-cache.php'),
            __DIR__ . '/../config/varbox-modules.php' => config_path('varbox/varbox-modules.php'),
            __DIR__ . '/../config/varbox-notification.php' => config_path('varbox/varbox-notification.php'),
            __DIR__ . '/../config/varbox-breadcrumb.php' => config_path('varbox/varbox-breadcrumb.php'),
            __DIR__ . '/../config/varbox-crud.php' => config_path('varbox/varbox-crud.php'),
            __DIR__ . '/../config/varbox-flash.php' => config_path('varbox/varbox-flash.php'),
            __DIR__ . '/../config/varbox-pagination.php' => config_path('varbox/varbox-pagination.php'),
            __DIR__ . '/../config/varbox-validation.php' => config_path('varbox/varbox-validation.php'),
        ], 'varbox-config');
    }

    /**
     * @return void
     */
    protected function overrideConfigs()
    {
        $this->config->set(
            'jsvalidation.view',
            config('varbox.varbox-validation.jsvaldidation_view', 'varbox::helpers.validation.trigger')
        );

        $this->config->set([
            'breadcrumbs.unnamed-route-exception' => $this->config['varbox']['varbox-breadcrumb']['throw_exceptions'] ?? true,
            'breadcrumbs.missing-route-bound-breadcrumb-exception' => $this->config['varbox']['varbox-breadcrumb']['throw_exceptions'] ?? true,
            'breadcrumbs.invalid-named-breadcrumb-exception' => $this->config['varbox']['varbox-breadcrumb']['throw_exceptions'] ?? true,
        ]);
    }

    /**
     * @return void
     */
    protected function publishMigrations()
    {
        if (empty(File::glob(database_path('migrations/*_create_varbox_tables.php')))) {
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../database/migrations/create_varbox_tables.php.stub' => database_path() . "/migrations/{$timestamp}_create_varbox_tables.php",
            ], 'varbox-migrations');
        }
    }

    /**
     * @return void
     */
    protected function publishViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'varbox');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/varbox'),
        ], 'varbox-views');
    }

    /**
     * @return void
     */
    protected function publishAssets()
    {
        $this->publishes([
            realpath(__DIR__ . '/../public/css') => public_path('vendor/varbox/css'),
            realpath(__DIR__ . '/../public/js') => public_path('vendor/varbox/js'),
            realpath(__DIR__ . '/../public/fonts') => public_path('vendor/varbox/fonts'),
            realpath(__DIR__ . '/../public/images') => public_path('vendor/varbox/images'),
        ], 'varbox-public');
    }

    /**
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                ActivityCleanCommand::class,
                NotificationsCleanCommand::class,
            ]);
        }
    }

    /**
     * @return void
     */
    protected function registerMiddlewares()
    {
        $this->router->aliasMiddleware('varbox.auth.session', AuthenticateSession::class);
        $this->router->aliasMiddleware('varbox.authenticated', Authenticated::class);
        $this->router->aliasMiddleware('varbox.not.authenticated', NotAuthenticated::class);
        $this->router->aliasMiddleware('varbox.check.roles', CheckRoles::class);
        $this->router->aliasMiddleware('varbox.check.permissions', CheckPermissions::class);
    }

    /**
     * @return void
     */
    protected function registerViewComposers()
    {
        $composers = $this->config['varbox.varbox-binding']['view_composers'];

        $this->app['view']->composer(
            'varbox::layouts.admin.partials._menu',
            $composers['admin_menu_view_composer'] ?? AdminMenuComposer::class
        );

        $this->app['view']->composer(
            'varbox::layouts.admin.partials._notifications',
            $composers['notifications_view_composer'] ?? NotificationsComposer::class
        );
    }

    /**
     * @return void
     */
    protected function registerRouteBindings()
    {
        Route::model('user', UserModelContract::class);
        Route::model('role', RoleModelContract::class);
        Route::model('permission', PermissionModelContract::class);
        Route::model('activity', ActivityModelContract::class);
    }

    /**
     * @return void
     */
    protected function loadRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/main.php');

        if (\Varbox::moduleEnabled('audit')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/audit.php');
        }
    }

    /**
     * @return void
     */
    protected function loadBreadcrumbs()
    {
        if ($this->config['varbox']['varbox-breadcrumb']['enabled'] ?? false === true) {
            require __DIR__ . '/../breadcrumbs/main.php';

            if (\Varbox::moduleEnabled('audit')) {
                require __DIR__ . '/../breadcrumbs/audit.php';
            }
        }
    }

    /**
     * @return void
     */
    protected function mergeConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/varbox-activity.php', 'varbox.varbox-activity');
        $this->mergeConfigFrom(__DIR__ . '/../config/varbox-cache.php', 'varbox.varbox-cache');
        $this->mergeConfigFrom(__DIR__ . '/../config/varbox-modules.php', 'varbox.varbox-modules');
        $this->mergeConfigFrom(__DIR__ . '/../config/varbox-notification.php', 'varbox.varbox-notification');
        $this->mergeConfigFrom(__DIR__ . '/../config/varbox-binding.php', 'varbox.varbox-binding');
        $this->mergeConfigFrom(__DIR__ . '/../config/varbox-breadcrumb.php', 'varbox.varbox-breadcrumb');
        $this->mergeConfigFrom(__DIR__ . '/../config/varbox-crud.php', 'varbox.varbox-crud');
        $this->mergeConfigFrom(__DIR__ . '/../config/varbox-flash.php', 'varbox.varbox-flash');
        $this->mergeConfigFrom(__DIR__ . '/../config/varbox-pagination.php', 'varbox.varbox-pagination');
        $this->mergeConfigFrom(__DIR__ . '/../config/varbox-validation.php', 'varbox.varbox-validation');
    }

    /**
     * @return void
     */
    protected function registerFacades()
    {
        $this->app->singleton('varbox', Varbox::class);
        $this->app->alias('Varbox', VarboxFacade::class);
    }

    /**
     * @return void
     */
    protected function registerServiceBindings()
    {
        $binding = $this->config['varbox.varbox-binding'];

        $this->app->singleton(QueryCacheServiceContract::class, $binding['services']['query_cache_service'] ?? QueryCacheService::class);
        $this->app->alias(QueryCacheServiceContract::class, 'query_cache.service');
    }

    /**
     * @return void
     */
    protected function registerModelBindings()
    {
        $binding = $this->config['varbox.varbox-binding'];

        $this->app->bind(UserModelContract::class, $binding['models']['user_model'] ?? User::class);
        $this->app->alias(UserModelContract::class, 'user.model');

        $this->app->bind(RoleModelContract::class, $binding['models']['role_model'] ?? Role::class);
        $this->app->alias(RoleModelContract::class, 'role.model');

        $this->app->bind(PermissionModelContract::class, $binding['models']['permission_model'] ?? Permission::class);
        $this->app->alias(PermissionModelContract::class, 'permission.model');

        $this->app->bind(ActivityModelContract::class, $binding['models']['activity_model'] ?? Activity::class);
        $this->app->alias(ActivityModelContract::class, 'activity.model');
    }

    /**
     * @return void
     */
    protected function registerHelperBindings()
    {
        $binding = $this->config['varbox.varbox-binding'];

        $this->app->singleton(AdminFormHelperContract::class, $binding['helpers']['admin_form_helper'] ?? AdminFormHelper::class);
        $this->app->alias(AdminFormHelperContract::class, 'admin_form.helper');

        $this->app->singleton(AdminMenuHelperContract::class, $binding['helpers']['admin_menu_helper'] ?? AdminMenuHelper::class);
        $this->app->alias(AdminMenuHelperContract::class, 'admin_menu.helper');

        $this->app->bind(FlashHelperContract::class, $binding['helpers']['flash_helper'] ?? FlashHelper::class);
        $this->app->alias(FlashHelperContract::class, 'flash.helper');

        $this->app->singleton(MetaHelperContract::class, $binding['helpers']['meta_helper'] ?? MetaHelper::class);
        $this->app->alias(MetaHelperContract::class, 'meta.helper');

        $this->app->singleton(ValidationHelperContract::class, $binding['helpers']['validation_helper'] ?? ValidationHelper::class);
        $this->app->alias(ValidationHelperContract::class, 'validation.helper');

        $this->app->singleton(PaginationHelperContract::class, $binding['helpers']['pagination_helper'] ?? PaginationHelper::class);
        $this->app->alias(PaginationHelperContract::class, 'pagination.helper');

        $this->app->singleton(ButtonHelperContract::class, $binding['helpers']['button_helper'] ?? ButtonHelper::class);
        $this->app->alias(ButtonHelperContract::class, 'button.helper');
    }

    /**
     * @return void
     */
    protected function registerBladeDirectives()
    {
        Blade::{'if'}('permission', function ($permission) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasPermission($permission));
        });

        Blade::{'if'}('haspermission', function ($permission) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasPermission($permission));
        });

        Blade::{'if'}('hasanypermission', function ($permissions) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasAnyPermission($permissions));
        });

        Blade::{'if'}('hasallpermissions', function ($permissions) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasAllPermissions($permissions));
        });

        Blade::{'if'}('role', function ($role) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasRole($role));
        });

        Blade::{'if'}('hasrole', function ($role) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasRole($role));
        });

        Blade::{'if'}('hasanyrole', function ($roles) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasAnyRole($roles));
        });

        Blade::{'if'}('hasallroles', function ($roles) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasAllRoles($roles));
        });
    }
}
