<?php

namespace Varbox;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Routing\ControllerDispatcher;
use Illuminate\Routing\Route as RouteRouter;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Str;
use Spatie\Backup\Events\BackupWasSuccessful;
use Varbox\Commands\ActivityCleanCommand;
use Varbox\Commands\BackupsCleanCommand;
use Varbox\Commands\BlockMakeCommand;
use Varbox\Commands\CrudMakeCommand;
use Varbox\Commands\ErrorsCleanCommand;
use Varbox\Commands\WysiwygLinkCommand;
use Varbox\Commands\InstallCommand;
use Varbox\Commands\MailMakeCommand;
use Varbox\Commands\NotificationsCleanCommand;
use Varbox\Composers\AdminMenuComposer;
use Varbox\Composers\LanguagesComposer;
use Varbox\Composers\NotificationsComposer;
use Varbox\Contracts\ActivityModelContract;
use Varbox\Contracts\AddressModelContract;
use Varbox\Contracts\AdminFormHelperContract;
use Varbox\Contracts\AdminFormLangHelperContract;
use Varbox\Contracts\AdminMenuHelperContract;
use Varbox\Contracts\AnalyticsModelContract;
use Varbox\Contracts\BackupModelContract;
use Varbox\Contracts\BlockHelperContract;
use Varbox\Contracts\BlockModelContract;
use Varbox\Contracts\CityModelContract;
use Varbox\Contracts\ConfigModelContract;
use Varbox\Contracts\CountryModelContract;
use Varbox\Contracts\EmailModelContract;
use Varbox\Contracts\ErrorModelContract;
use Varbox\Contracts\FlashHelperContract;
use Varbox\Contracts\LanguageModelContract;
use Varbox\Contracts\MenuModelContract;
use Varbox\Contracts\MetaHelperContract;
use Varbox\Contracts\PageModelContract;
use Varbox\Contracts\PermissionModelContract;
use Varbox\Contracts\RedirectModelContract;
use Varbox\Contracts\RevisionModelContract;
use Varbox\Contracts\RoleModelContract;
use Varbox\Contracts\SchemaHelperContract;
use Varbox\Contracts\SchemaModelContract;
use Varbox\Contracts\StateModelContract;
use Varbox\Contracts\TranslationModelContract;
use Varbox\Contracts\TranslationServiceContract;
use Varbox\Contracts\UploadedHelperContract;
use Varbox\Contracts\UploaderHelperContract;
use Varbox\Contracts\UploaderLangHelperContract;
use Varbox\Contracts\UploadModelContract;
use Varbox\Contracts\UploadServiceContract;
use Varbox\Contracts\UrlModelContract;
use Varbox\Contracts\UserModelContract;
use Varbox\Contracts\ValidationHelperContract;
use Varbox\Events\ErrorSavedSuccessfully;
use Varbox\Helpers\AdminFormHelper;
use Varbox\Helpers\AdminFormLangHelper;
use Varbox\Helpers\AdminMenuHelper;
use Varbox\Helpers\BlockHelper;
use Varbox\Helpers\FlashHelper;
use Varbox\Helpers\MetaHelper;
use Varbox\Helpers\SchemaHelper;
use Varbox\Helpers\UploadedHelper;
use Varbox\Helpers\UploaderHelper;
use Varbox\Helpers\UploaderLangHelper;
use Varbox\Helpers\ValidationHelper;
use Varbox\Listeners\SendErrorSavedEmail;
use Varbox\Listeners\StoreBackupToDatabase;
use Varbox\Commands\UploadsLinkCommand;
use Varbox\Middleware\Authenticated;
use Varbox\Middleware\AuthenticateSession;
use Varbox\Middleware\CheckPermissions;
use Varbox\Middleware\CheckRoles;
use Varbox\Middleware\IsTranslatable;
use Varbox\Middleware\NotAuthenticated;
use Varbox\Middleware\OptimizeImages;
use Varbox\Middleware\OverrideConfigs;
use Varbox\Middleware\PersistLocale;
use Varbox\Middleware\RedirectRequests;
use Varbox\Models\Activity;
use Varbox\Models\Address;
use Varbox\Models\Analytics;
use Varbox\Models\Backup;
use Varbox\Models\Block;
use Varbox\Models\City;
use Varbox\Models\Config;
use Varbox\Models\Country;
use Varbox\Models\Email;
use Varbox\Models\Error;
use Varbox\Models\Language;
use Varbox\Models\Menu;
use Varbox\Models\Page;
use Varbox\Models\Permission;
use Varbox\Models\Redirect;
use Varbox\Models\Revision;
use Varbox\Models\Role;
use Varbox\Models\Schema;
use Varbox\Models\State;
use Varbox\Models\Translation;
use Varbox\Models\Upload;
use Varbox\Models\Url;
use Varbox\Models\User;
use Varbox\Services\TranslationService;
use Varbox\Services\UploadService;

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
        $this->registerViewNamespaces();
        $this->registerBladeDirectives();
        $this->loadRoutes();
        $this->registerRoutes();
        $this->loadBreadcrumbs();
        $this->listenToEvents();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigs();
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
            __DIR__ . '/../config/admin.php' => config_path('varbox/admin.php'),
            __DIR__ . '/../config/activity.php' => config_path('varbox/activity.php'),
            __DIR__ . '/../config/backup.php' => config_path('varbox/backup.php'),
            __DIR__ . '/../config/bindings.php' => config_path('varbox/bindings.php'),
            __DIR__ . '/../config/errors.php' => config_path('varbox/errors.php'),
            __DIR__ . '/../config/query-cache.php' => config_path('varbox/query-cache.php'),
            __DIR__ . '/../config/config.php' => config_path('varbox/config.php'),
            __DIR__ . '/../config/notifications.php' => config_path('varbox/notifications.php'),
            __DIR__ . '/../config/breadcrumbs.php' => config_path('varbox/breadcrumbs.php'),
            __DIR__ . '/../config/crud.php' => config_path('varbox/crud.php'),
            __DIR__ . '/../config/flash.php' => config_path('varbox/flash.php'),
            __DIR__ . '/../config/validation.php' => config_path('varbox/validation.php'),
            __DIR__ . '/../config/upload.php' => config_path('varbox/upload.php'),
            __DIR__ . '/../config/wysiwyg.php' => config_path('varbox/wysiwyg.php'),
            __DIR__ . '/../config/emails.php' => config_path('varbox/emails.php'),
            __DIR__ . '/../config/blocks.php' => config_path('varbox/blocks.php'),
            __DIR__ . '/../config/pages.php' => config_path('varbox/pages.php'),
            __DIR__ . '/../config/menus.php' => config_path('varbox/menus.php'),
            __DIR__ . '/../config/analytics.php' => config_path('varbox/analytics.php'),
            __DIR__ . '/../config/redirect.php' => config_path('varbox/redirect.php'),
            __DIR__ . '/../config/translation.php' => config_path('varbox/translation.php'),
            __DIR__ . '/../config/schema.php' => config_path('varbox/schema.php'),
            __DIR__ . '/../config/meta.php' => config_path('varbox/meta.php'),
        ], 'varbox-config');
    }

    /**
     * @return void
     */
    protected function overrideConfigs()
    {
        $this->config->set([
            'image-optimizer.optimizers' => $this->config['varbox']['upload']['images']['optimizers'] ?? []
        ]);

        $this->config->set('laravel-ffmpeg', [
            'default_disk' => $this->config['varbox']['upload']['storage']['disk'] ?? 'local',
            'ffmpeg.binaries' => $this->config['varbox']['upload']['videos']['binaries']['ffmpeg'] ?? 'ffmpeg',
            'ffprobe.binaries' => $this->config['varbox']['upload']['videos']['binaries']['ffprobe'] ?? 'ffprobe',
        ]);

        /*$this->config->set([
            'jsvalidation.view' => $this->config['varbox']['validation']['jsvalidation_view'] ?? 'jsvalidation::bootstrap4',
        ]);*/

        $this->config->set([
            'breadcrumbs.unnamed-route-exception' => $this->config['varbox']['breadcrumbs']['throw_exceptions'] ?? true,
            'breadcrumbs.missing-route-bound-breadcrumb-exception' => $this->config['varbox']['breadcrumbs']['throw_exceptions'] ?? true,
            'breadcrumbs.invalid-named-breadcrumb-exception' => $this->config['varbox']['breadcrumbs']['throw_exceptions'] ?? true,
        ]);

        $this->config->set([
            'backup.backup.name' => $this->config['varbox']['backup']['name'] ?? 'VarBox',
            'backup.backup.source' => $this->config['varbox']['backup']['source'] ?? [],
            'backup.backup.destination' => $this->config['varbox']['backup']['destination'] ?? [],
            'backup.backup.database_dump_compressor' => $this->config['varbox']['backup']['database_dump_compressor'] ?? null,
            'backup.notifications.notifications' => $this->config['varbox']['backup']['notifications']['notifications'] ?? [],
            'backup.notifications.mail.to' => $this->config['varbox']['backup']['notifications']['email'] ?? '',
        ]);

        $this->config->set([
            'analytics.view_id' => $this->config['varbox']['analytics']['view_id'] ?? null,
            'analytics.service_account_credentials_json' => $this->config['varbox']['analytics']['credentials_json'] ?? storage_path('app/analytics/service-account-credentials.json'),
            'analytics.cache_lifetime_in_minutes' => $this->config['varbox']['analytics']['cache']['lifetime'] ?? 60 * 24,
            'analytics.cache.store' => $this->config['varbox']['analytics']['cache']['store'] ?? 'file',
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
                UploadsLinkCommand::class,
                WysiwygLinkCommand::class,
                CrudMakeCommand::class,
                MailMakeCommand::class,
                BlockMakeCommand::class,
                ActivityCleanCommand::class,
                NotificationsCleanCommand::class,
                ErrorsCleanCommand::class,
                BackupsCleanCommand::class,
            ]);
        }
    }

    /**
     * @return void
     */
    protected function registerMiddlewares()
    {
        $middleware = $this->config['varbox.bindings']['middleware'];

        $this->router->aliasMiddleware('varbox.auth.session', $middleware['authenticate_session_middleware'] ?? AuthenticateSession::class);
        $this->router->aliasMiddleware('varbox.authenticated', $middleware['authenticated_middleware'] ?? Authenticated::class);
        $this->router->aliasMiddleware('varbox.not.authenticated', $middleware['not_authenticated_middleware'] ?? NotAuthenticated::class);
        $this->router->aliasMiddleware('varbox.check.roles', $middleware['check_roles_middleware'] ?? CheckRoles::class);
        $this->router->aliasMiddleware('varbox.check.permissions', $middleware['check_permissions_middleware'] ?? CheckPermissions::class);
        $this->router->aliasMiddleware('varbox.override.configs', $middleware['override_configs_middleware'] ?? OverrideConfigs::class);
        $this->router->aliasMiddleware('varbox.optimize.images', $middleware['optimize_images_middleware'] ?? OptimizeImages::class);
        $this->router->aliasMiddleware('varbox.redirect.requests', $middleware['redirect_requests_middleware'] ?? RedirectRequests::class);
        $this->router->aliasMiddleware('varbox.persist.locale', $middleware['persist_locale_middleware'] ?? PersistLocale::class);
        $this->router->aliasMiddleware('varbox.is.translatable', $middleware['is_translatable_middleware'] ?? IsTranslatable::class);

        $this->router->prependMiddlewareToGroup('web', 'varbox.override.configs');
        $this->router->prependMiddlewareToGroup('web', 'varbox.optimize.images');
        $this->router->prependMiddlewareToGroup('web', 'varbox.redirect.requests');
        $this->router->pushMiddlewareToGroup('web', 'varbox.persist.locale');
    }

    /**
     * @return void
     */
    protected function registerViewComposers()
    {
        $composers = $this->config['varbox.bindings']['view_composers'];

        $this->app['view']->composer(
            'varbox::layouts.admin.partials._menu',
            $composers['admin_menu_view_composer'] ?? AdminMenuComposer::class
        );

        $this->app['view']->composer(
            'varbox::layouts.admin.partials._notifications',
            $composers['notifications_view_composer'] ?? NotificationsComposer::class
        );

        $this->app['view']->composer(
            'varbox::layouts.admin.partials._languages',
            $composers['languages_view_composer'] ?? LanguagesComposer::class
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
        Route::model('url', UrlModelContract::class);
        Route::model('upload', UploadModelContract::class);
        Route::model('revision', RevisionModelContract::class);
        Route::model('activity', ActivityModelContract::class);
        Route::model('country', CountryModelContract::class);
        Route::model('state', StateModelContract::class);
        Route::model('city', CityModelContract::class);
        Route::model('address', AddressModelContract::class);
        Route::model('config', ConfigModelContract::class);
        Route::model('error', ErrorModelContract::class);
        Route::model('backup', BackupModelContract::class);
        Route::model('menu', MenuModelContract::class);
        Route::model('menuParent', MenuModelContract::class);
        Route::model('language', LanguageModelContract::class);
        Route::model('translation', TranslationModelContract::class);
        Route::model('analytics', AnalyticsModelContract::class);
        Route::model('redirect', RedirectModelContract::class);
        Route::model('schema', SchemaModelContract::class);

        Route::bind('email', function ($id) {
            $query = app(EmailModelContract::class)->whereId($id);

            if ($this->isOnAdminRoute()) {
                $query->withDrafts();
            }

            return $query->first() ?? abort(404);
        });

        Route::bind('block', function ($id) {
            $query = app(BlockModelContract::class)->whereId($id);

            if ($this->isOnAdminRoute()) {
                $query->withDrafts();
            }

            return $query->first() ?? abort(404);
        });

        Route::bind('page', function ($id) {
            $query = app(PageModelContract::class)->whereId($id);

            if ($this->isOnAdminRoute()) {
                $query->withDrafts();
            }

            return $query->first() ?? abort(404);
        });

        Route::bind('pageParent', function ($id) {
            $query = app(PageModelContract::class)->whereId($id);

            if ($this->isOnAdminRoute()) {
                $query->withDrafts();
            }

            return $query->first() ?? abort(404);
        });
    }

    /**
     * @return void
     */
    protected function registerViewNamespaces()
    {
        foreach ((array)config('varbox.blocks.types', []) as $type => $options) {
            view()->addNamespace("blocks_{$type}", realpath(base_path($options['views_path'])));
        }
    }

    /**
     * @return void
     */
    protected function loadRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../routes/auth.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/home.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/users.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/admins.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/roles.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/permissions.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/countries.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/states.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/cities.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/addresses.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/activity.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/notifications.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/configs.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/errors.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/backups.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/uploads.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/revisions.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/emails.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/blocks.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/pages.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/menus.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/languages.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/translations.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/analytics.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/redirects.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/schema.php');
        $this->loadRoutesFrom(__DIR__ . '/../routes/wysiwyg.php');
    }

    /**
     * @return void
     */
    protected function registerRoutes()
    {
        Route::macro('varbox', function () {
            Route::fallback(function ($url = '/') {
                try {
                    $url = app(UrlModelContract::class)->whereUrl($url)->firstOrFail();
                    $model = $url->urlable;

                    if (! $model) {
                        throw new ModelNotFoundException;
                    }

                    $controller = $model->getUrlOptions()->routeController;
                    $action = $model->getUrlOptions()->routeAction;

                    return (new ControllerDispatcher(app()))->dispatch(
                        app(RouteRouter::class)->setAction([
                            'uses' => $controller.'@'.$action,
                            'model' => $model,
                        ]), app($controller), $action
                    );
                } catch (ModelNotFoundException $e) {
                    abort(404);
                }
            });
        });
    }

    /**
     * @return void
     */
    protected function loadBreadcrumbs()
    {
        if ($this->config['varbox']['breadcrumbs']['enabled'] ?? false === true) {
            require_once __DIR__ . '/../breadcrumbs/home.php';
            require_once __DIR__ . '/../breadcrumbs/users.php';
            require_once __DIR__ . '/../breadcrumbs/admins.php';
            require_once __DIR__ . '/../breadcrumbs/roles.php';
            require_once __DIR__ . '/../breadcrumbs/permissions.php';
            require_once __DIR__ . '/../breadcrumbs/countries.php';
            require_once __DIR__ . '/../breadcrumbs/states.php';
            require_once __DIR__ . '/../breadcrumbs/cities.php';
            require_once __DIR__ . '/../breadcrumbs/addresses.php';
            require_once __DIR__ . '/../breadcrumbs/activity.php';
            require_once __DIR__ . '/../breadcrumbs/notifications.php';
            require_once __DIR__ . '/../breadcrumbs/configs.php';
            require_once __DIR__ . '/../breadcrumbs/errors.php';
            require_once __DIR__ . '/../breadcrumbs/backups.php';
            require_once __DIR__ . '/../breadcrumbs/uploads.php';
            require_once __DIR__ . '/../breadcrumbs/emails.php';
            require_once __DIR__ . '/../breadcrumbs/blocks.php';
            require_once __DIR__ . '/../breadcrumbs/pages.php';
            require_once __DIR__ . '/../breadcrumbs/menus.php';
            require_once __DIR__ . '/../breadcrumbs/languages.php';
            require_once __DIR__ . '/../breadcrumbs/translations.php';
            require_once __DIR__ . '/../breadcrumbs/analytics.php';
            require_once __DIR__ . '/../breadcrumbs/redirects.php';
            require_once __DIR__ . '/../breadcrumbs/schema.php';
        }
    }

    /**
     * @return void
     */
    protected function listenToEvents()
    {
        Event::listen(ErrorSavedSuccessfully::class, SendErrorSavedEmail::class);
        Event::listen(BackupWasSuccessful::class, StoreBackupToDatabase::class);
    }

    /**
     * @return void
     */
    protected function mergeConfigs()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/admin.php', 'varbox.admin');
        $this->mergeConfigFrom(__DIR__ . '/../config/activity.php', 'varbox.activity');
        $this->mergeConfigFrom(__DIR__ . '/../config/backup.php', 'varbox.backup');
        $this->mergeConfigFrom(__DIR__ . '/../config/errors.php', 'varbox.errors');
        $this->mergeConfigFrom(__DIR__ . '/../config/query-cache.php', 'varbox.query-cache');
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'varbox.config');
        $this->mergeConfigFrom(__DIR__ . '/../config/notifications.php', 'varbox.notifications');
        $this->mergeConfigFrom(__DIR__ . '/../config/bindings.php', 'varbox.bindings');
        $this->mergeConfigFrom(__DIR__ . '/../config/breadcrumbs.php', 'varbox.breadcrumbs');
        $this->mergeConfigFrom(__DIR__ . '/../config/crud.php', 'varbox.crud');
        $this->mergeConfigFrom(__DIR__ . '/../config/flash.php', 'varbox.flash');
        $this->mergeConfigFrom(__DIR__ . '/../config/validation.php', 'varbox.validation');
        $this->mergeConfigFrom(__DIR__ . '/../config/upload.php', 'varbox.upload');
        $this->mergeConfigFrom(__DIR__ . '/../config/wysiwyg.php', 'varbox.wysiwyg');
        $this->mergeConfigFrom(__DIR__ . '/../config/emails.php', 'varbox.emails');
        $this->mergeConfigFrom(__DIR__ . '/../config/blocks.php', 'varbox.blocks');
        $this->mergeConfigFrom(__DIR__ . '/../config/pages.php', 'varbox.pages');
        $this->mergeConfigFrom(__DIR__ . '/../config/menus.php', 'varbox.menus');
        $this->mergeConfigFrom(__DIR__ . '/../config/analytics.php', 'varbox.analytics');
        $this->mergeConfigFrom(__DIR__ . '/../config/redirect.php', 'varbox.redirect');
        $this->mergeConfigFrom(__DIR__ . '/../config/translation.php', 'varbox.translation');
        $this->mergeConfigFrom(__DIR__ . '/../config/schema.php', 'varbox.schema');
        $this->mergeConfigFrom(__DIR__ . '/../config/meta.php', 'varbox.meta');
    }

    /**
     * @return void
     */
    protected function registerServiceBindings()
    {
        $binding = $this->config['varbox.bindings'];

        $this->app->singleton(UploadServiceContract::class, $binding['services']['upload_service'] ?? UploadService::class);
        $this->app->alias(UploadServiceContract::class, 'upload.service');

        $this->app->singleton(TranslationServiceContract::class, $binding['services']['translation_service'] ?? TranslationService::class);
        $this->app->alias(TranslationServiceContract::class, 'translation.service');
    }

    /**
     * @return void
     */
    protected function registerModelBindings()
    {
        $binding = $this->config['varbox.bindings'];

        $this->app->bind(UserModelContract::class, $binding['models']['user_model'] ?? User::class);
        $this->app->alias(UserModelContract::class, 'user.model');

        $this->app->bind(RoleModelContract::class, $binding['models']['role_model'] ?? Role::class);
        $this->app->alias(RoleModelContract::class, 'role.model');

        $this->app->bind(PermissionModelContract::class, $binding['models']['permission_model'] ?? Permission::class);
        $this->app->alias(PermissionModelContract::class, 'permission.model');

        $this->app->bind(UrlModelContract::class, $binding['models']['url_model'] ?? Url::class);
        $this->app->alias(UrlModelContract::class, 'url.model');

        $this->app->bind(UploadModelContract::class, $binding['models']['upload_model'] ?? Upload::class);
        $this->app->alias(UploadModelContract::class, 'upload.model');

        $this->app->bind(RevisionModelContract::class, $this->config['models']['revision_model'] ?? Revision::class);
        $this->app->alias(RevisionModelContract::class, 'revision.model');

        $this->app->bind(ActivityModelContract::class, $binding['models']['activity_model'] ?? Activity::class);
        $this->app->alias(ActivityModelContract::class, 'activity.model');

        $this->app->bind(CountryModelContract::class, $binding['models']['country_model'] ?? Country::class);
        $this->app->alias(CountryModelContract::class, 'country.model');

        $this->app->bind(StateModelContract::class, $binding['models']['state_model'] ?? State::class);
        $this->app->alias(StateModelContract::class, 'state.model');

        $this->app->bind(CityModelContract::class, $binding['models']['city_model'] ?? City::class);
        $this->app->alias(CityModelContract::class, 'city.model');

        $this->app->bind(AddressModelContract::class, $binding['models']['address_model'] ?? Address::class);
        $this->app->alias(AddressModelContract::class, 'address.model');

        $this->app->bind(ConfigModelContract::class, $binding['models']['config_model'] ?? Config::class);
        $this->app->alias(ConfigModelContract::class, 'config.model');

        $this->app->bind(ErrorModelContract::class, $binding['models']['error_model'] ?? Error::class);
        $this->app->alias(ErrorModelContract::class, 'error.model');

        $this->app->bind(BackupModelContract::class, $binding['models']['backup_model'] ?? Backup::class);
        $this->app->alias(BackupModelContract::class, 'backup.model');

        $this->app->bind(EmailModelContract::class, $binding['models']['email_model'] ?? Email::class);
        $this->app->alias(EmailModelContract::class, 'email.model');

        $this->app->bind(BlockModelContract::class, $binding['models']['block_model'] ?? Block::class);
        $this->app->alias(BlockModelContract::class, 'block.model');

        $this->app->bind(PageModelContract::class, $binding['models']['page_model'] ?? Page::class);
        $this->app->alias(PageModelContract::class, 'page.model');

        $this->app->bind(MenuModelContract::class, $binding['models']['menu_model'] ?? Menu::class);
        $this->app->alias(MenuModelContract::class, 'menu.model');

        $this->app->bind(LanguageModelContract::class, $binding['models']['language_model'] ?? Language::class);
        $this->app->alias(LanguageModelContract::class, 'language.model');

        $this->app->bind(TranslationModelContract::class, $binding['models']['translation_model'] ?? Translation::class);
        $this->app->alias(TranslationModelContract::class, 'translation.model');

        $this->app->bind(AnalyticsModelContract::class, $binding['models']['analytics_model'] ?? Analytics::class);
        $this->app->alias(AnalyticsModelContract::class, 'analytics.model');

        $this->app->bind(RedirectModelContract::class, $binding['models']['redirect_model'] ?? Redirect::class);
        $this->app->alias(RedirectModelContract::class, 'redirect.model');

        $this->app->bind(SchemaModelContract::class, $binding['models']['schema_model'] ?? Schema::class);
        $this->app->alias(SchemaModelContract::class, 'schema.model');
    }

    /**
     * @return void
     */
    protected function registerHelperBindings()
    {
        $binding = $this->config['varbox.bindings'];

        $this->app->singleton(AdminFormHelperContract::class, $binding['helpers']['admin_form_helper'] ?? AdminFormHelper::class);
        $this->app->alias(AdminFormHelperContract::class, 'admin_form.helper');

        $this->app->singleton(AdminFormLangHelperContract::class, $binding['helpers']['admin_form_lang_helper'] ?? AdminFormLangHelper::class);
        $this->app->alias(AdminFormLangHelperContract::class, 'admin_form_lang.helper');

        $this->app->singleton(AdminMenuHelperContract::class, $binding['helpers']['admin_menu_helper'] ?? AdminMenuHelper::class);
        $this->app->alias(AdminMenuHelperContract::class, 'admin_menu.helper');

        $this->app->bind(FlashHelperContract::class, $binding['helpers']['flash_helper'] ?? FlashHelper::class);
        $this->app->alias(FlashHelperContract::class, 'flash.helper');

        $this->app->singleton(MetaHelperContract::class, $binding['helpers']['meta_helper'] ?? MetaHelper::class);
        $this->app->alias(MetaHelperContract::class, 'meta.helper');

        $this->app->singleton(ValidationHelperContract::class, $binding['helpers']['validation_helper'] ?? ValidationHelper::class);
        $this->app->alias(ValidationHelperContract::class, 'validation.helper');

        $this->app->singleton(UploadedHelperContract::class, $binding['helpers']['uploaded_helper'] ?? UploadedHelper::class);
        $this->app->alias(UploadedHelperContract::class, 'uploaded.helper');

        $this->app->singleton(UploaderHelperContract::class, $binding['helpers']['uploader_helper'] ?? UploaderHelper::class);
        $this->app->alias(UploaderHelperContract::class, 'uploader.helper');

        $this->app->singleton(UploaderLangHelperContract::class, $binding['helpers']['uploader_lang_helper'] ?? UploaderLangHelper::class);
        $this->app->alias(UploaderLangHelperContract::class, 'uploader_lang.helper');

        $this->app->singleton(BlockHelperContract::class, $binding['helpers']['block_helper'] ?? BlockHelper::class);
        $this->app->alias(BlockHelperContract::class, 'block.helper');

        $this->app->singleton(SchemaHelperContract::class, $binding['helpers']['schema_helper'] ?? SchemaHelper::class);
        $this->app->alias(SchemaHelperContract::class, 'schema.helper');
    }

    /**
     * @return void
     */
    protected function registerBladeDirectives()
    {
        Blade::if('permission', function ($permission) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasPermission($permission));
        });

        Blade::if('haspermission', function ($permission) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasPermission($permission));
        });

        Blade::if('hasanypermission', function ($permissions) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasAnyPermission($permissions));
        });

        Blade::if('hasallpermissions', function ($permissions) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasAllPermissions($permissions));
        });

        Blade::if('role', function ($role) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasRole($role));
        });

        Blade::if('hasrole', function ($role) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasRole($role));
        });

        Blade::if('hasanyrole', function ($roles) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasAnyRole($roles));
        });

        Blade::if('hasallroles', function ($roles) {
            return auth()->check() && (auth()->user()->isSuper() || auth()->user()->hasAllRoles($roles));
        });

        Blade::directive('analytics', function () {
            return "<?php echo optional(app('analytics.model')->first())->code ?: '' ?>";
        });

        Blade::directive('schema', function ($expression) {
            return "<?php echo app('schema.helper')->renderAll($expression); ?>";
        });
    }

    /**
     * @return bool
     */
    protected function isOnAdminRoute()
    {
        return Str::startsWith(Route::current()->uri(), config('varbox.admin.prefix', 'admin') . '/');
    }
}
