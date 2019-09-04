<?php

namespace Varbox\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class BlockMakeCommand extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'varbox:make-block 
                            {type : The type of the block} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the necessary files for a block';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     *
     * return void
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        if ($this->alreadyExists()) {
            $this->error('There is already a block with the name of "' . $this->argument('type') . '".');

            return false;
        }

        $this->createBlockFiles();
        $this->addBlockTypeToConfig();

        $this->info('Block created successfully inside the "app/Blocks/' . $this->argument('type') . '/" directory!');
        $this->comment('<bg=yellow> </> The new block type was added inside the "config/varbox/blocks.php" file.');

        return true;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['type', InputArgument::REQUIRED, 'The type of the block.'],
        ];
    }

    /*
     * Get the path of the composer file.
     *
     * @return string
     */
    protected function getComposerFile()
    {
        return "{$this->getPath()}/Composer.php";
    }

    /*
     * Get the path of the admin view file.
     *
     * @return string
     */
    protected function getAdminViewFile()
    {
        return "{$this->getPath()}/Views/admin.blade.php";
    }

    /*
     * Get the path of the front view file.
     *
     * @return string
     */
    protected function getFrontViewFile()
    {
        return "{$this->getPath()}/Views/front.blade.php";
    }

    /**
     * Get the composer stub file for the generator.
     *
     * @return string
     */
    protected function getComposerStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/blocks/composer.stub';
    }

    /**
     * Get the admin view stub file for the generator.
     *
     * @return string
     */
    protected function getAdminViewStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/blocks/admin.view.stub';
    }

    /**
     * Get the front view stub file for the generator.
     *
     * @return string
     */
    protected function getFrontViewStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/blocks/front.view.stub';
    }

    /**
     * Get the front view stub file for the generator.
     *
     * @return string
     */
    protected function getMultipleViewStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/blocks/multiple.view.stub';
    }

    /**
     * Get the application path.
     *
     * @return string
     */
    protected function getPath()
    {
        $name = str_replace('\\', '/', str_replace($this->laravel->getNamespace(), '', $this->argument('type')));

        return "{$this->laravel['path']}/Blocks/{$name}";
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @return void
     */
    protected function createBlockFiles()
    {
        $this->makeDirectories($this->getPath());

        $this->files->put($this->getComposerFile(), $this->buildComposer());
        $this->files->put($this->getAdminViewFile(), $this->buildAdminView());
        $this->files->put($this->getFrontViewFile(), $this->buildFrontView());
    }

    /**
     * Add the block type to the "config/varbox/blocks.php" -> "type" config key.
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @return void
     */
    protected function addBlockTypeToConfig()
    {
        $config = $this->laravel['path.config'] . '/varbox/blocks.php';

        if ($this->files->exists($config)) {
            $content = $this->files->get($config);

            if (strpos($content, "'" . $this->argument('type') . "' => [") === false) {
                $content = str_replace(
                    "'types' => [",
                    "'types' => [\n\n" . $this->buildTypeConfig()
                    , $content
                );

                $this->files->put($config, $content);
            }
        }
    }

    /**
     * Determine if the module files already exists.
     *
     * @return bool
     */
    protected function alreadyExists()
    {
        return
            $this->files->exists($this->getComposerFile()) ||
            $this->files->exists($this->getAdminViewFile()) ||
            $this->files->exists($this->getFrontViewFile());
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param string $path
     * @return string
     */
    protected function makeDirectories($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0755, true, true);
        }

        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }

        if (!$this->files->isDirectory($path . '/Views')) {
            $this->files->makeDirectory($path . '/Views', 0755, true, true);
        }
    }

    /**
     * Get the block's composer contents.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildComposer()
    {
        if ($this->option('no-interaction') == true) {
            $locations = null;
        } else {
            $locationsQuestion = [];
            $locationsQuestion[] = 'What are the locations this block should be available in?';
            $locationsQuestion[] = ' <fg=white>Please delimit the locations by using a space <fg=yellow>" "</> between them.</>';
            $locationsQuestion[] = ' <fg=white>If you don\'t want any locations, just hit <fg=yellow>ENTER</></>';

            $locations = $this->ask(implode(PHP_EOL, $locationsQuestion));
        }

        if ($locations) {
            $locations = "'" . str_replace(" ", "', '", $locations) . "'";
        }

        $content = $this->files->get($this->getComposerStub());
        $content = str_replace('DummyNamespace', 'App\Blocks\\' . $this->argument('type'), $content);
        $content = str_replace('dummy_locations', $locations ?: '', $content);

        return $content;
    }

    /**
     * Get the block's admin view contents.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildAdminView()
    {
        if ($this->option('no-interaction') == true) {
            $dummy = 'no';
        } else {
            $dummyQuestion = [];
            $dummyQuestion[] = 'Do you want to generate dummy fields for the admin view?';
            $dummyQuestion[] = ' <fg=white>If you choose <fg=yellow>yes</>, the script will generate one example input field for each type available in the platform</>';

            $dummy = $this->choice(implode(PHP_EOL, $dummyQuestion), [
                true => 'yes', false => 'no'
            ], true);
        }

        $content = '';

        if ($dummy == 'yes') {
            $content .= $this->files->get($this->getAdminViewStub());
        }

        if ($this->option('no-interaction') == true) {
            $multiple = 'no';
        } else {
            $multipleQuestion = [];
            $multipleQuestion[] = 'Do you want support for multiple items inside the admin view?';
            $multipleQuestion[] = ' <fg=white>If you choose <fg=yellow>yes</>, the script will generate the code needed for adding multiple items (like a list) to the block</>';

            $multiple = $this->choice(implode(PHP_EOL, $multipleQuestion), [
                true => 'yes', false => 'no'
            ], true);
        }

        if ($multiple == 'yes') {
            $content .= "\n" . $this->files->get($this->getMultipleViewStub());
        }

        return $content;
    }

    /**
     * Get the block's front view contents.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildFrontView()
    {
        return $this->files->get($this->getFrontViewStub());
    }

    /**
     * Build the config block for the block type.
     *
     * @return string
     */
    protected function buildTypeConfig()
    {
        $content = [];
        $type = $this->argument('type');
        $namespace = $this->getAppNamespace();

        $content[] = "        '{$type}' => [\n";
        $content[] = "            'label' => '" . Str::title($type) . " Block',\n";
        $content[] = "            'composer_class' => '{$namespace}Blocks\\{$type}\Composer',\n";
        $content[] = "            'views_path' => 'app/Blocks/{$type}/Views',\n";
        $content[] = "            'preview_image' => '',\n";
        $content[] = "        ],";

        return implode('', $content);
    }
}
