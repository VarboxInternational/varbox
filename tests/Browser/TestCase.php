<?php

namespace Varbox\Tests\Browser;

use Collective\Html\FormFacade;
use Collective\Html\HtmlFacade;
use Collective\Html\HtmlServiceProvider;
use DaveJamesMiller\Breadcrumbs\BreadcrumbsServiceProvider;
use DaveJamesMiller\Breadcrumbs\Facades\Breadcrumbs;
use Illuminate\Contracts\Foundation\Application;
use Orchestra\Testbench\Dusk\Options as OrchestraDuskOptions;
use Orchestra\Testbench\Dusk\TestCase as OrchestraDuskTestCase;
use Proengsoft\JsValidation\Facades\JsValidatorFacade;
use Proengsoft\JsValidation\JsValidationServiceProvider;
use Varbox\Facades\VarboxFacade;
use Varbox\Models\Role;
use Varbox\Models\User;
use Varbox\Tests\Browser\Browsers\VarboxBrowser;
use Varbox\VarboxServiceProvider;

abstract class TestCase extends OrchestraDuskTestCase
{
    /**
     * Server IP.
     *
     * @var string
     */
    protected static $baseServeHost = '127.0.0.1';

    /**
     * Server PORT.
     *
     * @var int
     */
    protected static $baseServePort = 9000;

    /**
     * The admin user.
     *
     * @var User
     */
    protected $admin;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp(): void
    {
        // !IMPORTANT!
        // before running "phpunit", run the following commands:
        // - php vendor/orchestra/testbench-dusk/create-sqlite-db
        // - ./vendor/bin/dusk-updater update

        parent::setUp();

        OrchestraDuskOptions::withoutUI();

        $this->setUpDatabase($this->app);

        $this->afterApplicationCreated(function () {
            $this->installVarboxPlatform();
            $this->initAdminUser();
        });
    }

    /**
     * Register the package service providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            VarboxServiceProvider::class,
            HtmlServiceProvider::class,
            JsValidationServiceProvider::class,
            BreadcrumbsServiceProvider::class,
        ];
    }

    /**
     * Register the package facades.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Varbox' => VarboxFacade::class,
            'Form' => FormFacade::class,
            'Html' => HtmlFacade::class,
            'Breadcrumbs' => Breadcrumbs::class,
            'JsValidator' => JsValidatorFacade::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('auth.providers.users.model', User::class);
    }

    /**
     * Set up the database and migrate the necessary tables.
     *
     * @param  $app
     */
    protected function setUpDatabase(Application $app)
    {
        $this->loadLaravelMigrations('sqlite');
    }

    /**
     * Create a new Browser instance.
     *
     * @param  \Facebook\WebDriver\Remote\RemoteWebDriver  $driver
     * @return \Varbox\Tests\Browser\Browsers\VarboxBrowser
     */
    protected function newBrowser($driver)
    {
        return new VarboxBrowser($driver);
    }

    /**
     * Run the command to install the VarBox platform.
     *
     * @return void
     */
    protected function installVarboxPlatform()
    {
        $this->artisan('varbox:install');
    }

    /**
     * Set up and admin user to be used throughout.
     *
     * @return void
     */
    protected function initAdminUser()
    {
        $this->admin = User::first();

        $this->admin->removeRoles(Role::all());
        $this->admin->revokePermission(Role::all());

        $this->admin->assignRoles('Admin');
    }
}
