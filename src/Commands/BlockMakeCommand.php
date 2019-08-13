<?php

namespace Varbox\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;

class BlockMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'varbox:make-block 
                            {name : The name of the block type} ';

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
        $path = $this->getPath();

        $composerFile = "{$path}/Composer.php";
        $adminViewFile = "{$path}/Views/admin.blade.php";
        $frontViewFile = "{$path}/Views/front.blade.php";

        if ($this->alreadyExists($composerFile, $adminViewFile, $frontViewFile)) {
            $this->error('There is already a block with the name of "' . $this->argument('name') . '".');

            return false;
        }

        $this->makeDirectories($path);

        $this->files->put($composerFile, $this->buildComposer());
        $this->files->put($adminViewFile, $this->buildAdminView());
        $this->files->put($frontViewFile, $this->buildFrontView());

        $this->info('Block created successfully inside the "app/Blocks/' . $this->argument('name') . '/" directory!');
        $this->comment('<bg=yellow> </> Don\'t forget to add your newly created block type to the "types" key inside the "config/varbox/blocks.php" file.');

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
            ['name', InputArgument::REQUIRED, 'The name of the block.'],
        ];
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
        $name = str_replace('\\', '/', str_replace($this->laravel->getNamespace(), '', $this->argument('name')));

        return "{$this->laravel['path']}/Blocks/{$name}";
    }

    /**
     * Get the block's composer contents.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildComposer()
    {
        $locationsQuestion = [];
        $locationsQuestion[] = 'What are the locations this block should be available in?';
        $locationsQuestion[] = ' <fg=white>Please delimit the locations by using a space <fg=yellow>" "</> between them.</>';
        $locationsQuestion[] = ' <fg=white>If you don\'t want any locations, just hit <fg=yellow>ENTER</></>';

        $locations = $this->ask(implode(PHP_EOL, $locationsQuestion));

        if ($locations) {
            $locations = "'" . str_replace(" ", "', '", $locations) . "'";
        }

        $content = $this->files->get($this->getComposerStub());
        $content = str_replace('DummyNamespace', 'App\Blocks\\' . $this->argument('name'), $content);
        $content = str_replace('dummy_locations', $locations ?: '', $content);

        return $content;
        ;
    }

    /**
     * Get the block's admin view contents.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildAdminView()
    {
        $dummyQuestion = [];
        $dummyQuestion[] = 'Do you want to generate dummy fields for the admin view?';
        $dummyQuestion[] = ' <fg=white>If you choose <fg=yellow>yes</>, the script will generate one example input field for each type available in the platform</>';

        $dummy = $this->choice(implode(PHP_EOL, $dummyQuestion), [
            true => 'yes', false => 'no'
        ], true);

        $content = '';

        if ($dummy == 'yes') {
            $content .= $this->files->get($this->getAdminViewStub());
        }

        $multipleQuestion = [];
        $multipleQuestion[] = 'Do you want support for multiple items inside the admin view?';
        $multipleQuestion[] = ' <fg=white>If you choose <fg=yellow>yes</>, the script will generate the code needed for adding multiple items (like a list) to the block</>';

        $multiple = $this->choice(implode(PHP_EOL, $multipleQuestion), [
            true => 'yes', false => 'no'
        ], true);

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
     * Determine if the module files already exists.
     *
     * @param string $composerFile
     * @param string $adminViewFile
     * @param string $frontViewFile
     * @return bool
     */
    protected function alreadyExists($composerFile, $adminViewFile, $frontViewFile)
    {
        return
            $this->files->exists($composerFile) ||
            $this->files->exists($adminViewFile) ||
            $this->files->exists($frontViewFile);
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
}
