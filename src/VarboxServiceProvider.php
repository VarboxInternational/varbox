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
use Varbox\Contracts\ActivityFilterContract;
use Varbox\Contracts\ActivityModelContract;
use Varbox\Contracts\ActivitySortContract;
use Varbox\Contracts\AddressFilterContract;
use Varbox\Contracts\AddressModelContract;
use Varbox\Contracts\AddressSortContract;
use Varbox\Contracts\AdminFilterContract;
use Varbox\Contracts\AdminFormHelperContract;
use Varbox\Contracts\AdminFormLangHelperContract;
use Varbox\Contracts\AdminSortContract;
use Varbox\Contracts\BackupFilterContract;
use Varbox\Contracts\BackupSortContract;
use Varbox\Contracts\BlockFilterContract;
use Varbox\Contracts\BlockSortContract;
use Varbox\Contracts\CityFilterContract;
use Varbox\Contracts\CitySortContract;
use Varbox\Contracts\ConfigFilterContract;
use Varbox\Contracts\ConfigSortContract;
use Varbox\Contracts\CountryFilterContract;
use Varbox\Contracts\CountrySortContract;
use Varbox\Contracts\EmailFilterContract;
use Varbox\Contracts\EmailSortContract;
use Varbox\Contracts\ErrorFilterContract;
use Varbox\Contracts\ErrorSortContract;
use Varbox\Contracts\LanguageFilterContract;
use Varbox\Contracts\LanguageSortContract;
use Varbox\Contracts\MenuFilterContract;
use Varbox\Contracts\MenuHelperContract;
use Varbox\Contracts\BackupModelContract;
use Varbox\Contracts\BlockModelContract;
use Varbox\Contracts\CityModelContract;
use Varbox\Contracts\ConfigModelContract;
use Varbox\Contracts\CountryModelContract;
use Varbox\Contracts\EmailModelContract;
use Varbox\Contracts\ErrorModelContract;
use Varbox\Contracts\FlashHelperContract;
use Varbox\Contracts\LanguageModelContract;
use Varbox\Contracts\MenuModelContract;
use Varbox\Contracts\MenuSortContract;
use Varbox\Contracts\MetaHelperContract;
use Varbox\Contracts\PageFilterContract;
use Varbox\Contracts\PageModelContract;
use Varbox\Contracts\PageSortContract;
use Varbox\Contracts\PermissionFilterContract;
use Varbox\Contracts\PermissionModelContract;
use Varbox\Contracts\PermissionSortContract;
use Varbox\Contracts\RedirectFilterContract;
use Varbox\Contracts\RedirectModelContract;
use Varbox\Contracts\RedirectSortContract;
use Varbox\Contracts\RevisionModelContract;
use Varbox\Contracts\RoleFilterContract;
use Varbox\Contracts\RoleModelContract;
use Varbox\Contracts\RoleSortContract;
use Varbox\Contracts\StateFilterContract;
use Varbox\Contracts\StateModelContract;
use Varbox\Contracts\StateSortContract;
use Varbox\Contracts\TranslationFilterContract;
use Varbox\Contracts\TranslationModelContract;
use Varbox\Contracts\TranslationServiceContract;
use Varbox\Contracts\TranslationSortContract;
use Varbox\Contracts\UploadedHelperContract;
use Varbox\Contracts\UploaderHelperContract;
use Varbox\Contracts\UploaderLangHelperContract;
use Varbox\Contracts\UploadFilterContract;
use Varbox\Contracts\UploadModelContract;
use Varbox\Contracts\UploadServiceContract;
use Varbox\Contracts\UploadSortContract;
use Varbox\Contracts\UrlModelContract;
use Varbox\Contracts\UserFilterContract;
use Varbox\Contracts\UserModelContract;
use Varbox\Contracts\UserSortContract;
use Varbox\Events\ErrorSavedSuccessfully;
use Varbox\Filters\ActivityFilter;
use Varbox\Filters\AddressFilter;
use Varbox\Filters\AdminFilter;
use Varbox\Filters\BackupFilter;
use Varbox\Filters\BlockFilter;
use Varbox\Filters\CityFilter;
use Varbox\Filters\ConfigFilter;
use Varbox\Filters\CountryFilter;
use Varbox\Filters\EmailFilter;
use Varbox\Filters\ErrorFilter;
use Varbox\Filters\LanguageFilter;
use Varbox\Filters\MenuFilter;
use Varbox\Filters\PageFilter;
use Varbox\Filters\PermissionFilter;
use Varbox\Filters\RedirectFilter;
use Varbox\Filters\RoleFilter;
use Varbox\Filters\StateFilter;
use Varbox\Filters\TranslationFilter;
use Varbox\Filters\UploadFilter;
use Varbox\Filters\UserFilter;
use Varbox\Helpers\AdminFormHelper;
use Varbox\Helpers\AdminFormLangHelper;
use Varbox\Helpers\MenuHelper;
use Varbox\Helpers\FlashHelper;
use Varbox\Helpers\MetaHelper;
use Varbox\Helpers\UploadedHelper;
use Varbox\Helpers\UploaderHelper;
use Varbox\Helpers\UploaderLangHelper;
use Varbox\Listeners\SendErrorSavedEmail;
use Varbox\Listeners\StoreBackupToDatabase;
use Varbox\Commands\UploadsLinkCommand;
use Varbox\Middleware\Authenticated;
use Varbox\Middleware\AuthenticateSession;
use Varbox\Middleware\CheckPermissions;
use Varbox\Middleware\CheckRoles;
use Varbox\Middleware\IsTranslatable;
use Varbox\Middleware\NotAuthenticated;
use Varbox\Middleware\OverwriteConfigs;
use Varbox\Middleware\PersistLocale;
use Varbox\Middleware\RedirectRequests;
use Varbox\Models\Activity;
use Varbox\Models\Address;
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
use Varbox\Models\State;
use Varbox\Models\Translation;
use Varbox\Models\Upload;
use Varbox\Models\Url;
use Varbox\Models\User;
use Varbox\Services\TranslationService;
use Varbox\Services\UploadService;
use Varbox\Sorts\ActivitySort;
use Varbox\Sorts\AddressSort;
use Varbox\Sorts\AdminSort;
use Varbox\Sorts\BackupSort;
use Varbox\Sorts\BlockSort;
use Varbox\Sorts\CitySort;
use Varbox\Sorts\ConfigSort;
use Varbox\Sorts\CountrySort;
use Varbox\Sorts\EmailSort;
use Varbox\Sorts\ErrorSort;
use Varbox\Sorts\LanguageSort;
use Varbox\Sorts\MenuSort;
use Varbox\Sorts\PageSort;
use Varbox\Sorts\PermissionSort;
use Varbox\Sorts\RedirectSort;
use Varbox\Sorts\RoleSort;
use Varbox\Sorts\StateSort;
use Varbox\Sorts\TranslationSort;
use Varbox\Sorts\UploadSort;
use Varbox\Sorts\UserSort;

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
        $this->OverwriteConfigs();
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
        $this->registerFilterBindings();
        $this->registerSortBindings();
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
            __DIR__ . '/../config/crud.php' => config_path('varbox/crud.php'),
            __DIR__ . '/../config/flash.php' => config_path('varbox/flash.php'),
            __DIR__ . '/../config/upload.php' => config_path('varbox/upload.php'),
            __DIR__ . '/../config/wysiwyg.php' => config_path('varbox/wysiwyg.php'),
            __DIR__ . '/../config/emails.php' => config_path('varbox/emails.php'),
            __DIR__ . '/../config/blocks.php' => config_path('varbox/blocks.php'),
            __DIR__ . '/../config/pages.php' => config_path('varbox/pages.php'),
            __DIR__ . '/../config/menus.php' => config_path('varbox/menus.php'),
            __DIR__ . '/../config/redirect.php' => config_path('varbox/redirect.php'),
            __DIR__ . '/../config/translation.php' => config_path('varbox/translation.php'),
            __DIR__ . '/../config/meta.php' => config_path('varbox/meta.php'),
        ], 'varbox-config');
    }

    /**
     * @return void
     */
    protected function OverwriteConfigs()
    {
        $this->config->set([
            'backup.backup.name' => $this->config['varbox']['backup']['name'] ?? 'VarBox',
            'backup.backup.source' => $this->config['varbox']['backup']['source'] ?? [],
            'backup.backup.destination' => $this->config['varbox']['backup']['destination'] ?? [],
            'backup.backup.database_dump_compressor' => $this->config['varbox']['backup']['database_dump_compressor'] ?? null,
            'backup.notifications.notifications' => $this->config['varbox']['backup']['notifications']['notifications'] ?? [],
            'backup.notifications.mail.to' => $this->config['varbox']['backup']['notifications']['email'] ?? '',
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
                __DIR__ . '/../database/migrations/create_varbox_tables.stub' => database_path() . "/migrations/{$timestamp}_create_varbox_tables.php",
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
        $this->router->aliasMiddleware('varbox.overwrite.configs', $middleware['overwrite_configs_middleware'] ?? OverwriteConfigs::class);
        $this->router->aliasMiddleware('varbox.redirect.requests', $middleware['redirect_requests_middleware'] ?? RedirectRequests::class);
        $this->router->aliasMiddleware('varbox.persist.locale', $middleware['persist_locale_middleware'] ?? PersistLocale::class);
        $this->router->aliasMiddleware('varbox.is.translatable', $middleware['is_translatable_middleware'] ?? IsTranslatable::class);

        /*$this->router->prependMiddlewareToGroup('web', 'varbox.overwrite.configs');
        $this->router->prependMiddlewareToGroup('web', 'varbox.redirect.requests');
        $this->router->pushMiddlewareToGroup('web', 'varbox.persist.locale');*/
    }

    /**
     * @return void
     */
    protected function registerViewComposers()
    {
        $composers = $this->config['varbox.bindings']['view_composers'];

        $this->app['view']->composer(
            'varbox::layouts.partials._menu',
            $composers['admin_menu_view_composer'] ?? AdminMenuComposer::class
        );

        $this->app['view']->composer(
            'varbox::layouts.partials._notifications',
            $composers['notifications_view_composer'] ?? NotificationsComposer::class
        );

        $this->app['view']->composer(
            'varbox::layouts.partials._languages',
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
        Route::model('redirect', RedirectModelContract::class);

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
            view()->composer("blocks_{$type}::front", $options['composer_class']);
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
        $this->loadRoutesFrom(__DIR__ . '/../routes/redirects.php');
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
        $this->mergeConfigFrom(__DIR__ . '/../config/crud.php', 'varbox.crud');
        $this->mergeConfigFrom(__DIR__ . '/../config/flash.php', 'varbox.flash');
        $this->mergeConfigFrom(__DIR__ . '/../config/upload.php', 'varbox.upload');
        $this->mergeConfigFrom(__DIR__ . '/../config/wysiwyg.php', 'varbox.wysiwyg');
        $this->mergeConfigFrom(__DIR__ . '/../config/emails.php', 'varbox.emails');
        $this->mergeConfigFrom(__DIR__ . '/../config/blocks.php', 'varbox.blocks');
        $this->mergeConfigFrom(__DIR__ . '/../config/pages.php', 'varbox.pages');
        $this->mergeConfigFrom(__DIR__ . '/../config/menus.php', 'varbox.menus');
        $this->mergeConfigFrom(__DIR__ . '/../config/redirect.php', 'varbox.redirect');
        $this->mergeConfigFrom(__DIR__ . '/../config/translation.php', 'varbox.translation');
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

        $this->app->bind(RedirectModelContract::class, $binding['models']['redirect_model'] ?? Redirect::class);
        $this->app->alias(RedirectModelContract::class, 'redirect.model');
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

        $this->app->singleton(MenuHelperContract::class, $binding['helpers']['menu_helper'] ?? MenuHelper::class);
        $this->app->alias(MenuHelperContract::class, 'menu.helper');

        $this->app->bind(FlashHelperContract::class, $binding['helpers']['flash_helper'] ?? FlashHelper::class);
        $this->app->alias(FlashHelperContract::class, 'flash.helper');

        $this->app->singleton(MetaHelperContract::class, $binding['helpers']['meta_helper'] ?? MetaHelper::class);
        $this->app->alias(MetaHelperContract::class, 'meta.helper');

        $this->app->singleton(UploadedHelperContract::class, $binding['helpers']['uploaded_helper'] ?? UploadedHelper::class);
        $this->app->alias(UploadedHelperContract::class, 'uploaded.helper');

        $this->app->singleton(UploaderHelperContract::class, $binding['helpers']['uploader_helper'] ?? UploaderHelper::class);
        $this->app->alias(UploaderHelperContract::class, 'uploader.helper');

        $this->app->singleton(UploaderLangHelperContract::class, $binding['helpers']['uploader_lang_helper'] ?? UploaderLangHelper::class);
        $this->app->alias(UploaderLangHelperContract::class, 'uploader_lang.helper');
    }

    /**
     * @return void
     */
    protected function registerFilterBindings()
    {
        $binding = $this->config['varbox.bindings'];

        $this->app->singleton(ActivityFilterContract::class, $binding['filters']['activity_filter'] ?? ActivityFilter::class);
        $this->app->singleton(AddressFilterContract::class, $binding['filters']['address_filter'] ?? AddressFilter::class);
        $this->app->singleton(AdminFilterContract::class, $binding['filters']['admin_filter'] ?? AdminFilter::class);
        $this->app->singleton(BackupFilterContract::class, $binding['filters']['backup_filter'] ?? BackupFilter::class);
        $this->app->singleton(BlockFilterContract::class, $binding['filters']['block_filter'] ?? BlockFilter::class);
        $this->app->singleton(CityFilterContract::class, $binding['filters']['city_filter'] ?? CityFilter::class);
        $this->app->singleton(ConfigFilterContract::class, $binding['filters']['config_filter'] ?? ConfigFilter::class);
        $this->app->singleton(CountryFilterContract::class, $binding['filters']['country_filter'] ?? CountryFilter::class);
        $this->app->singleton(EmailFilterContract::class, $binding['filters']['email_filter'] ?? EmailFilter::class);
        $this->app->singleton(ErrorFilterContract::class, $binding['filters']['error_filter'] ?? ErrorFilter::class);
        $this->app->singleton(LanguageFilterContract::class, $binding['filters']['language_filter'] ?? LanguageFilter::class);
        $this->app->singleton(MenuFilterContract::class, $binding['filters']['menu_filter'] ?? MenuFilter::class);
        $this->app->singleton(PageFilterContract::class, $binding['filters']['page_filter'] ?? PageFilter::class);
        $this->app->singleton(PermissionFilterContract::class, $binding['filters']['permission_filter'] ?? PermissionFilter::class);
        $this->app->singleton(RedirectFilterContract::class, $binding['filters']['redirect_filter'] ?? RedirectFilter::class);
        $this->app->singleton(RoleFilterContract::class, $binding['filters']['role_filter'] ?? RoleFilter::class);
        $this->app->singleton(StateFilterContract::class, $binding['filters']['state_filter'] ?? StateFilter::class);
        $this->app->singleton(TranslationFilterContract::class, $binding['filters']['translations_filter'] ?? TranslationFilter::class);
        $this->app->singleton(UploadFilterContract::class, $binding['filters']['upload_filter'] ?? UploadFilter::class);
        $this->app->singleton(UserFilterContract::class, $binding['filters']['user_filter'] ?? UserFilter::class);
    }

    /**
     * @return void
     */
    protected function registerSortBindings()
    {
        $binding = $this->config['varbox.bindings'];

        $this->app->singleton(ActivitySortContract::class, $binding['sorts']['activity_sort'] ?? ActivitySort::class);
        $this->app->singleton(AddressSortContract::class, $binding['sorts']['address_sort'] ?? AddressSort::class);
        $this->app->singleton(AdminSortContract::class, $binding['sorts']['admin_sort'] ?? AdminSort::class);
        $this->app->singleton(BackupSortContract::class, $binding['sorts']['backup_sort'] ?? BackupSort::class);
        $this->app->singleton(BlockSortContract::class, $binding['sorts']['block_sort'] ?? BlockSort::class);
        $this->app->singleton(CitySortContract::class, $binding['sorts']['city_sort'] ?? CitySort::class);
        $this->app->singleton(ConfigSortContract::class, $binding['sorts']['config_sort'] ?? ConfigSort::class);
        $this->app->singleton(CountrySortContract::class, $binding['sorts']['country_sort'] ?? CountrySort::class);
        $this->app->singleton(EmailSortContract::class, $binding['sorts']['email_sort'] ?? EmailSort::class);
        $this->app->singleton(ErrorSortContract::class, $binding['sorts']['error_sort'] ?? ErrorSort::class);
        $this->app->singleton(LanguageSortContract::class, $binding['sorts']['language_sort'] ?? LanguageSort::class);
        $this->app->singleton(MenuSortContract::class, $binding['sorts']['menu_sort'] ?? MenuSort::class);
        $this->app->singleton(PageSortContract::class, $binding['sorts']['page_sort'] ?? PageSort::class);
        $this->app->singleton(PermissionSortContract::class, $binding['sorts']['permission_sort'] ?? PermissionSort::class);
        $this->app->singleton(RedirectSortContract::class, $binding['sorts']['redirect_sort'] ?? RedirectSort::class);
        $this->app->singleton(RoleSortContract::class, $binding['sorts']['role_sort'] ?? RoleSort::class);
        $this->app->singleton(StateSortContract::class, $binding['sorts']['state_sort'] ?? StateSort::class);
        $this->app->singleton(TranslationSortContract::class, $binding['sorts']['translations_sort'] ?? TranslationSort::class);
        $this->app->singleton(UploadSortContract::class, $binding['sorts']['upload_sort'] ?? UploadSort::class);
        $this->app->singleton(UserSortContract::class, $binding['sorts']['user_sort'] ?? UserSort::class);
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
    }

    /**
     * @return bool
     */
    protected function isOnAdminRoute()
    {
        return Str::startsWith(Route::current()->uri(), config('varbox.admin.prefix', 'admin') . '/');
    }
}
