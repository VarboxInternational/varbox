<?php

namespace Varbox\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Schema;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.n
     *
     * @var string
     */
    protected $signature = 'varbox:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the VarBox platform.';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The composer instance.
     *
     * @var Composer
     */
    protected $composer;

    /**
     * Create a new controller creator command instance.
     *
     * @param Filesystem $files
     * @param Composer $composer
     */
    public function __construct(Filesystem $files, Composer $composer)
    {
        parent::__construct();

        $this->files = $files;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $this->brand();

        $this->line('<fg=cyan>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=cyan>Installing the VarBox platform.</>');
        $this->line('<fg=cyan>-------------------------------------------------------------------------------------------------------</>');

        $this->publishFiles();
        $this->writeEnvVariables();
        $this->registerRoutes();
        $this->modifyUserModel();
        $this->modifyExceptionHandler();
        $this->generateAdminMenu();
        $this->generatePages();
        $this->manageUploads();
        $this->manageBackups();
        $this->manageWysiwyg();
        $this->setupPasswordResets();
        $this->overwriteBindings();
        $this->copySeedersAndSql();
        $this->dumpComposerAutoload();
    }

    /**
     * @return void
     */
    protected function publishFiles()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>PUBLISHING FILES</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        $this->callSilent('vendor:publish', ['--tag' => 'varbox-config']);
        $this->line('<fg=green>SUCCESS |</> Published all config files inside the "config/varbox/" directory.');

        $this->callSilent('vendor:publish', ['--tag' => 'varbox-migrations']);
        $this->line('<fg=green>SUCCESS |</> Published all migration files inside the "database/migrations/" directory.');

        $this->callSilent('vendor:publish', ['--tag' => 'varbox-views']);
        $this->line('<fg=green>SUCCESS |</> Published all view files inside the "resources/views/vendor/varbox/" directory.');

        $this->callSilent('vendor:publish', ['--tag' => 'varbox-public']);
        $this->line('<fg=green>SUCCESS |</> Published all asset files inside the "public/vendor/varbox/" directory.');
    }

    /**
     * @return void
     */
    protected function writeEnvVariables()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>UPDATING .ENV FILE</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        try {
            $env = $this->files->get($this->laravel->environmentFilePath());

            if (false === strpos($env, 'VARBOX_LICENSE_CODE')) {
                $this->files->append($this->laravel->environmentFilePath(), "\nVARBOX_LICENSE_CODE=");
                $this->line('<fg=green>SUCCESS |</> Appended "VARBOX_LICENSE_CODE" configuration to the ".env" file!');
            } else {
                $this->line('<fg=green>SUCCESS |</> The ".env" file already contains the "VARBOX_LICENSE_CODE" configuration.');
            }

            if (false === strpos($env, 'CACHE_ALL_QUERIES')) {
                $this->files->append($this->laravel->environmentFilePath(), "\nCACHE_ALL_QUERIES=false");
                $this->line('<fg=green>SUCCESS |</> Appended "CACHE_ALL_QUERIES" configuration to the ".env" file!');
            } else {
                $this->line('<fg=green>SUCCESS |</> The ".env" file already contains the "CACHE_ALL_QUERIES" configuration.');
            }

            if (false === strpos($env, 'CACHE_DUPLICATE_QUERIES')) {
                $this->files->append($this->laravel->environmentFilePath(), "\nCACHE_DUPLICATE_QUERIES=false\n");
                $this->line('<fg=green>SUCCESS |</> Appended "CACHE_DUPLICATE_QUERIES" configuration to the ".env" file!');
            } else {
                $this->line('<fg=green>SUCCESS |</> The ".env" file already contains the "CACHE_DUPLICATE_QUERIES" configuration.');
            }

            if (false === strpos($env, 'LOG_ACTIVITY')) {
                $this->files->append($this->laravel->environmentFilePath(), "\nLOG_ACTIVITY=false");
                $this->line('<fg=green>SUCCESS |</> Appended "LOG_ACTIVITY" configuration to the ".env" file!');
            } else {
                $this->line('<fg=green>SUCCESS |</> The ".env" file already contains the "LOG_ACTIVITY" configuration.');
            }

            if (false === strpos($env, 'SAVE_ERRORS')) {
                $this->files->append($this->laravel->environmentFilePath(), "\nSAVE_ERRORS=false");
                $this->line('<fg=green>SUCCESS |</> Appended "SAVE_ERRORS" configuration to the ".env" file!');
            } else {
                $this->line('<fg=green>SUCCESS |</> The ".env" file already contains the "SAVE_ERRORS" configuration.');
            }
        } catch (FileNotFoundException $e) {
            $this->line('<fg=red>ERROR   |</> Unable to append the env variables! The file ".env" was not found.');
        }
    }

    /**
     * @return void
     */
    protected function registerRoutes()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>REGISTERING ROUTE</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        try {
            $routes = $this->files->get(base_path('routes/web.php'));

            if (false === strpos($routes, 'Route::varbox()')) {
                $this->files->append(base_path('routes/web.php'), "\n// This should be the last line\nRoute::varbox();\n");
                $this->line('<fg=green>SUCCESS |</> Registered the routes inside the "routes/web.php" file.');
            } else {
                $this->line('<fg=green>SUCCESS |</> Route already registered inside the "routes/web.php" file.');
            }
        } catch (FileNotFoundException $e) {
            $this->line('<fg=red>ERROR   |</> Unable to register the route`! The file "routes/web.php" was not found.');
        }
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    protected function modifyUserModel()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>MODIFYING USER MODEL</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        $authGuardsStub = __DIR__ . '/../../resources/stubs/config/auth/guards.stub';
        $userModelFile = $this->laravel['path'] . '/Models/User.php';
        $authConfig = $this->laravel['path.config'] . '/auth.php';

        if ($this->files->exists($authConfig)) {
            $content = $this->files->get($authConfig);

            if (strpos($content, "'admin' => [") === false) {
                $content = str_replace(
                    "'guards' => [",
                    "'guards' => [\n\n" . file_get_contents($authGuardsStub)
                    , $content
                );

                $this->files->put($authConfig, $content);

                $this->line('<fg=green>SUCCESS |</> Added the "admin" guard inside "config/auth.php" => "guards".');
            } else {
                $this->line('<fg=green>SUCCESS |</> The "admin" guard already exists inside "config/auth.php" => "guards".');
            }
        } else {
            $this->line('<fg=red>ERROR   |</> The "config/auth.php" file does not exist!');
            $this->line('<fg=red>ERROR   |</> You will have to manually add the "admin" guard (same as "users").');
        }

        if ($this->files->exists($userModelFile)) {
            $content = $this->files->get($userModelFile);

            if ($content !== false) {
                $content = str_replace(
                    'extends Authenticatable',
                    "extends \Varbox\Models\User",
                    $content
                );

                $content = str_replace(
                    "'name', 'email', 'password'",
                    "'name', 'email', 'password', 'active'",
                    $content
                );

                if (strpos($content, "protected \$casts = [\n        'active' => 'boolean',") === false) {
                    $content = str_replace(
                        "protected \$casts = [",
                        "protected \$casts = [\n        'active' => 'boolean',",
                        $content
                    );
                }

                $this->files->put($userModelFile, $content);

                $this->line('<fg=green>SUCCESS |</> Extended the "app/Models/User.php" with the VarBox user model.');
                $this->line('<fg=green>SUCCESS |</> Modified the "fillable" property of the "app/Models/User.php".');
                $this->line('<fg=green>SUCCESS |</> Modified the "casts" property of the "app/Models/User.php".');
            } else {
                $this->line('<fg=red>ERROR   |</> Could not get the contents of "app/Models/User.php"! You will need to update this manually.');
                $this->line('<fg=red>ERROR   |</> Change "extends Authenticatable" to "extends \Varbox\Models\User" in your user model.');
                $this->line('<fg=red>ERROR   |</> Append to the "fillable" property the following fields: active');
                $this->line('<fg=red>ERROR   |</> Append to the "casts" property the following: "active" => "boolean"');
            }
        } else {
            $this->line('<fg=red>ERROR   |</> Unable to locate "app/Models/User.php"! You will need to update this manually.');
            $this->line('<fg=red>ERROR   |</> Change "extends Authenticatable" to "extends \Varbox\Models\User" in your user model.');
            $this->line('<fg=red>ERROR   |</> Append to the "fillable" property the following fields: active');
            $this->line('<fg=red>ERROR   |</> Append to the "casts" property the following: "active" => "boolean"');
        }
    }

    /**
     * @throws FileNotFoundException
     */
    public function modifyExceptionHandler()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>MODIFYING THE EXCEPTION HANDLER</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        $exceptionHandlerFile = $this->laravel['path'] . '/Exceptions/Handler.php';

        if ($this->files->exists($exceptionHandlerFile)) {
            $content = $this->files->get($exceptionHandlerFile);

            if ($content !== false) {
                $content = str_replace(
                    'extends ExceptionHandler',
                    "extends \Varbox\Exceptions\Handler",
                    $content
                );

                $this->files->put($exceptionHandlerFile, $content);

                $this->line('<fg=green>SUCCESS |</> Extended the "app/Exceptions/Handler.php" with the VarBox exception handler.');
            } else {
                $this->line('<fg=red>ERROR   |</> Could not get the contents of "app/Exceptions/Handler.php"! You will need to update this manually.');
                $this->line('<fg=red>ERROR   |</> Change "extends ExceptionHandler" to "extends \Varbox\Exceptions\Handler" in your exception handler.');
            }
        } else {
            $this->line('<fg=red>ERROR   |</> Unable to locate "app/Exceptions/Handler.php"! You will need to update this manually.');
            $this->line('<fg=red>ERROR   |</> Change "extends ExceptionHandler" to "extends \Varbox\Exceptions\Handler" in your exception handler.');
        }
    }

    /**
     * @return bool
     * @throws FileNotFoundException
     */
    protected function generateAdminMenu()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>GENERATING ADMIN MENU</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        $stub = __DIR__ . '/../../resources/stubs/composers/admin_menu.stub';
        $path = "{$this->laravel['path']}/Http/Composers";
        $file = "{$path}/AdminMenuComposer.php";
        $contents = str_replace('DummyNamespace', $this->laravel->getNamespace() . 'Http\\Composers', $this->files->get($stub));

        if ($this->files->exists($file)) {
            $this->line('<fg=green>SUCCESS |</> The file "AdminMenuComposer.php" already exists inside the "app/Http/Composers/" directory!');

            return false;
        }

        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }

        $this->files->put($file, $contents);
        $this->line('<fg=green>SUCCESS |</> The "AdminMenuComposer.php" file has been copied over to "app/Http/Composers/" directory!');
    }

    /**
     * @return bool
     * @throws FileNotFoundException
     */
    protected function generatePages()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>GENERATING PAGES ROUTER</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        $stub = __DIR__ . '/../../resources/stubs/controllers/pages.stub';
        $path = "{$this->laravel['path']}/Http/Controllers";
        $file = "{$path}/PagesController.php";
        $contents = str_replace('DummyNamespace', $this->laravel->getNamespace() . 'Http\\Controllers', $this->files->get($stub));

        if ($this->files->exists($file)) {
            $this->line('<fg=green>SUCCESS |</> The file "PagesController.php" already exists inside the "app/Http/Controllers/" directory!');

            return false;
        }

        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }

        $this->files->put($file, $contents);
        $this->line('<fg=green>SUCCESS |</> The "PagesController.php" file has been copied over to "app/Http/Controllers/" directory!');
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    protected function manageUploads()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>MANAGING UPLOADS</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        $uploadsDiskStub = __DIR__ . '/../../resources/stubs/config/upload/disks.stub';
        $filesystemsConfig = $this->laravel['path.config'] . '/filesystems.php';
        $uploadsPath = $this->laravel['path.storage'] . '/uploads';
        $gitignoreFile = $uploadsPath . '/.gitignore';

        if ($this->files->exists($filesystemsConfig)) {
            $content = $this->files->get($filesystemsConfig);

            if (strpos($content, "'uploads' => [") === false) {
                $content = str_replace(
                    "'disks' => [",
                    "'disks' => [\n\n" . file_get_contents($uploadsDiskStub)
                    , $content
                );

                $this->files->put($filesystemsConfig, $content);
                $this->line('<fg=green>SUCCESS |</> Setup the "uploads" storage disk inside "config/filesystems.php" => "disks".');
            } else {
                $this->line('<fg=green>SUCCESS |</> The "uploads" storage disk already exists inside "config/filesystems.php" => "disks".');
            }
        } else {
            $this->line('<fg=red>ERROR   |</> The "config/filesystems.php" file does not exist!');
            $this->line('<fg=red>ERROR   |</> You will have to manually add the "uploads" storage disk.');
        }

        if ($this->files->exists($gitignoreFile)) {
            $this->line('<fg=green>SUCCESS |</> The "storage/uploads/" directory already exists.');
            $this->line('<fg=green>SUCCESS |</> The ".gitignore" file inside the "storage/uploads/" directory already exists.');

            return;
        } else {
            $this->files->makeDirectory($uploadsPath);
            $this->line('<fg=green>SUCCESS |</> Created the "storage/uploads/" directory!');

            $this->files->put($gitignoreFile, "*\n!.gitignore\n");
            $this->line('<fg=green>SUCCESS |</> Created the ".gitignore" file inside "storage/uploads/" directory!');

            $this->callSilent('varbox:uploads-link');
            $this->line('<fg=green>SUCCESS |</> The "public/uploads/" directory has been linked!');
        }
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    protected function manageBackups()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>MANAGING BACKUPS</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        $backupsDiskStub = __DIR__ . '/../../resources/stubs/config/backup/disks.stub';
        $filesystemsConfig = $this->laravel['path.config'] . '/filesystems.php';
        $backupsPath = $this->laravel['path.storage'] . '/backups';
        $gitignoreFile = $backupsPath . '/.gitignore';

        if ($this->files->exists($filesystemsConfig)) {
            $content = $this->files->get($filesystemsConfig);

            if (strpos($content, "'backups' => [") === false) {
                $content = str_replace(
                    "'disks' => [",
                    "'disks' => [\n\n" . file_get_contents($backupsDiskStub)
                    , $content
                );

                $this->files->put($filesystemsConfig, $content);
                $this->line('<fg=green>SUCCESS |</> Setup the "backups" storage disk inside "config/filesystems.php" => "disks".');
            } else {
                $this->line('<fg=green>SUCCESS |</> The "backups" storage disk already exists inside "config/filesystems.php" => "disks".');
            }
        } else {
            $this->line('<fg=red>ERROR   |</> The "config/filesystems.php" file does not exist!');
            $this->line('<fg=red>ERROR   |</> You will have to manually add the "backups" storage disk.');
        }

        if ($this->files->exists($gitignoreFile)) {
            $this->line('<fg=green>SUCCESS |</> The "storage/backups/" directory already exists.');
            $this->line('<fg=green>SUCCESS |</> The ".gitignore" file inside the "storage/backups/" directory already exists.');

            return;
        } else {
            $this->files->makeDirectory($backupsPath);
            $this->line('<fg=green>SUCCESS |</> Created the "storage/backups/" directory!');

            $this->files->put($gitignoreFile, "*\n!.gitignore\n");
            $this->line('<fg=green>SUCCESS |</> Created the ".gitignore" file inside "storage/backups/" directory!');
        }
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    protected function manageWysiwyg()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>SETTING UP WYSIWYG</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        $wysiwygDiskStub = __DIR__ . '/../../resources/stubs/config/wysiwyg/disks.stub';
        $filesystemsConfig = $this->laravel['path.config'] . '/filesystems.php';
        $wysiwygPath = $this->laravel['path.storage'] . '/wysiwyg';
        $gitignoreFile = $wysiwygPath . '/.gitignore';

        if ($this->files->exists($filesystemsConfig)) {
            $content = $this->files->get($filesystemsConfig);

            if (strpos($content, "'wysiwyg' => [") === false) {
                $content = str_replace(
                    "'disks' => [",
                    "'disks' => [\n\n" . file_get_contents($wysiwygDiskStub)
                    , $content
                );

                $this->files->put($filesystemsConfig, $content);
                $this->line('<fg=green>SUCCESS |</> Setup the "wysiwyg" storage disk inside "config/filesystems.php" => "disks".');
            } else {
                $this->line('<fg=green>SUCCESS |</> The "wysiwyg" storage disk already exists inside "config/filesystems.php" => "disks".');
            }
        } else {
            $this->line('<fg=red>ERROR   |</> The "config/filesystems.php" file does not exist!');
            $this->line('<fg=red>ERROR   |</> You will have to manually add the "wysiwyg" storage disk.');
        }

        if ($this->files->exists($gitignoreFile)) {
            $this->line('<fg=green>SUCCESS |</> The "storage/wysiwyg/" directory already exists.');
            $this->line('<fg=green>SUCCESS |</> The ".gitignore" file inside the "storage/wysiwyg/" directory already exists.');

            return;
        } else {
            $this->files->makeDirectory($wysiwygPath);
            $this->line('<fg=green>SUCCESS |</> Created the "storage/wysiwyg/" directory!');

            $this->files->put($gitignoreFile, "*\n!.gitignore\n");
            $this->line('<fg=green>SUCCESS |</> Created the ".gitignore" file inside "storage/wysiwyg/" directory!');

            $this->callSilent('varbox:wysiwyg-link');
            $this->line('<fg=green>SUCCESS |</> The "public/wysiwyg/" directory has been linked!');
        }
    }

    /**
     * @return void
     */
    protected function setupPasswordResets()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>SETTING UP PASSWORD RESETS</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        if (Schema::hasTable('password_resets')) {
            $this->line('<fg=green>SUCCESS |</> The "password_resets" table already exists.');

            return;
        }

        $vendorMigrationFile = base_path('vendor/laravel/ui/stubs/migrations/2014_10_12_100000_create_password_resets_table.php');
        $localMigrationFile = base_path('database/migrations/2014_10_12_100000_create_password_resets_table.php');

        if ($this->files->exists($localMigrationFile)) {
            $this->line('<fg=green>SUCCESS |</> The "create_password_resets_table" migration already exists.');

            return;
        }

        if (!$this->files->exists($vendorMigrationFile)) {
            $this->line('<fg=red>ERROR |</> The "vendor/laravel/ui/stubs/migrations/2014_10_12_100000_create_password_resets_table.php" does not exist!');
            $this->line('<fg=red>ERROR |</> Install the "laravel/ui" composer package and run "php artisan varbox:install" again.');
        }

        copy($vendorMigrationFile, $localMigrationFile);

        $this->line('<fg=green>SUCCESS |</> The "create_password_resets_table" migration file was successfully copied from "laravel/ui".');
    }

    /**
     * @return void
     */
    protected function copySeedersAndSql()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>COPYING SEEDERS AND SQL</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        $this->files->ensureDirectoryExists(database_path('seeders'));

        if (!$this->files->exists(database_path('seeders/PermissionsSeeder.php'))) {
            copy(__DIR__ . '/../../database/seeders/PermissionsSeeder.stub', database_path('seeders/PermissionsSeeder.php'));
        }

        if (!$this->files->exists(database_path('seeders/RolesSeeder.php'))) {
            copy(__DIR__ . '/../../database/seeders/RolesSeeder.stub', database_path('seeders/RolesSeeder.php'));
        }

        if (!$this->files->exists(database_path('seeders/UsersSeeder.php'))) {
            copy(__DIR__ . '/../../database/seeders/UsersSeeder.stub', database_path('seeders/UsersSeeder.php'));
        }

        if (!$this->files->exists(database_path('seeders/LanguagesSeeder.php'))) {
            copy(__DIR__ . '/../../database/seeders/LanguagesSeeder.stub', database_path('seeders/LanguagesSeeder.php'));
        }

        if (!$this->files->exists(database_path('seeders/CountriesSeeder.php'))) {
            copy(__DIR__ . '/../../database/seeders/CountriesSeeder.stub', database_path('seeders/CountriesSeeder.php'));
        }

        if (!$this->files->exists(database_path('seeders/StatesSeeder.php'))) {
            copy(__DIR__ . '/../../database/seeders/StatesSeeder.stub', database_path('seeders/StatesSeeder.php'));
        }

        if (!$this->files->exists(database_path('seeders/CitiesSeeder.php'))) {
            copy(__DIR__ . '/../../database/seeders/CitiesSeeder.stub', database_path('seeders/CitiesSeeder.php'));
        }

        if (!$this->files->exists(database_path('seeders/VarboxSeeder.php'))) {
            copy(__DIR__ . '/../../database/seeders/VarboxSeeder.stub', database_path('seeders/VarboxSeeder.php'));
        }

        $this->line('<fg=green>SUCCESS |</> Copied all seeders inside the "database/seeders/" directory.');

        $this->files->ensureDirectoryExists(database_path('sql'));

        if (!$this->files->exists(database_path('sql/countries.sql'))) {
            copy(__DIR__ . '/../../database/sql/countries.sql', database_path('sql/countries.sql'));
        }

        if (!$this->files->exists(database_path('sql/states.sql'))) {
            copy(__DIR__ . '/../../database/sql/states.sql', database_path('sql/states.sql'));
        }

        if (!$this->files->exists(database_path('sql/cities.sql'))) {
            copy(__DIR__ . '/../../database/sql/cities.sql', database_path('sql/cities.sql'));
        }

        $this->line('<fg=green>SUCCESS |</> Copied all sql files inside the "database/sql/" directory.');
    }

    /**
     * @return void
     */
    protected function dumpComposerAutoload()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>DUMPING COMPOSER AUTOLOAD</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        $this->composer->dumpAutoloads();

        $this->line('<fg=green>SUCCESS |</> Dumped composer autoload.');
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    protected function overwriteBindings()
    {
        $this->line(PHP_EOL . PHP_EOL);
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');
        $this->line('<fg=yellow>OVERWRITING CLASS BINDINGS</>');
        $this->line('<fg=yellow>-------------------------------------------------------------------------------------------------------</>');

        $bindingsConfigFile = $this->laravel->configPath('varbox/bindings.php');

        if ($this->files->exists($bindingsConfigFile)) {
            $content = $this->files->get($bindingsConfigFile);

            if ($content !== false) {
                if (class_exists('\App\Models\User')) {
                    $content = str_replace(
                        '\Varbox\Models\User::class',
                        "\App\Models\User::class",
                        $content
                    );
                }

                if (class_exists('\App\Http\Composers\AdminMenuComposer')) {
                    $content = str_replace(
                        '\Varbox\Composers\AdminMenuComposer::class',
                        "\App\Http\Composers\AdminMenuComposer::class",
                        $content
                    );
                }

                $this->files->put($bindingsConfigFile, $content);

                $this->line('<fg=green>SUCCESS |</> Overwritten the "admin_menu_view_composer" inside the "config/varbox/bindings.php" file.');
                $this->line('<fg=green>SUCCESS |</> Overwritten the "user_model" inside the "config/varbox/bindings.php" file.');
            } else {
                $this->line('<fg=red>ERROR   |</> Could not get the contents of "config/varbox/bindings.php"! You will need to update this manually.');
                $this->line('<fg=red>ERROR   |</> Change "user_model" value to "\App\Models\User::class" in your bindings config file.');
                $this->line('<fg=red>ERROR   |</> Change "admin_menu_view_composer" value to "\App\Http\Composers\AdminMenuComposer::class" in your bindings config file.');
            }
        } else {
            $this->line('<fg=red>ERROR   |</> Unable to locate "config/varbox/bindings.php"! You will need to update this manually.');
            $this->line('<fg=red>ERROR   |</> Change "user_model" value to "\App\Models\User::class" in your bindings config file.');
            $this->line('<fg=red>ERROR   |</> Change "admin_menu_view_composer" value to "\App\Http\Composers\AdminMenuComposer::class" in your bindings config file.');
        }
    }

    /**
     * http://patorjk.com/software/taag/#p=display&f=Small&t=varbox%20base
     *
     * @return void
     */
    protected function brand()
    {
        $this->line(
            "<fg=cyan>               _
 __ ____ _ _ _| |__  _____ __
 \ V / _` | '_| '_ \/ _ \ \ /
  \_/\__,_|_| |_.__/\___/_\_\
</>"
        );
    }
}
