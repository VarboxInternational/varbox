<?php

namespace Varbox\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CrudMakeCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'varbox:make-crud';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an entire CRUD functionality';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * @var string
     */
    protected $modelClass;

    /**
     * @var string
     */
    protected $viewsPath;

    /**
     * @var bool
     */
    protected $useDraft;

    /**
     * @var bool
     */
    protected $useRevisions;

    /**
     * @var bool
     */
    protected $useDuplicate;

    /**
     * @var bool
     */
    protected $usePreview;

    /**
     * @var bool
     */
    protected $useOrder;

    /**
     * Create a new command instance.
     *
     * return void
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct($files);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->askQuestions();
        $this->guardAgainstExistingFiles();

        $this->createRoutes();
        $this->createController();

        $name = $this->qualifyClass($this->modelClass);
        $path = $this->getPath($name);

        dd($name, $path);
    }






    /**
     * @return void
     */
    protected function askQuestions()
    {
        $this->modelClass = $this->askForModelClass();

        if (!$this->modelClass || !Str::startsWith($this->modelClass, $this->laravel->getNamespace())) {
            $this->error('Invalid model FQN provided');
            $this->line('It should start with: <fg=yellow>' . $this->laravel->getNamespace());
            die;
        }

        $this->viewsPath = $this->askForViewsPath();

        if (!$this->viewsPath) {
            $this->error('Please provide a views path');
            die;
        }

        $this->useDraft = $this->askForUsingDraft();
        $this->useRevisions = $this->askForUsingRevisions();
        $this->useDuplicate = $this->askForUsingDuplicate();
        $this->usePreview = $this->askForUsingPreview();
        $this->useOrder = $this->askForUsingOrder();
    }

    /**
     * @return void
     */
    protected function guardAgainstExistingFiles()
    {
        if (!$this->files->exists($this->getRoutesFile())) {
            $this->error('The "routes/web.php" file was not found!');
            die;
        }

        if ($this->files->exists($this->getControllerFile())) {
            $this->error('The controller "' . $this->fullControllerNamespace() . '" already exists!');
            die;
        }
    }







    /**
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function createRoutes()
    {
        $content = $this->files->get($this->getRoutesFile());

        $content = str_replace('// This should be the last line', '', $content);
        $content = str_replace('Route::varbox();', '', $content);

        $content .= $this->buildRoutes();
        $content .= "\n// This should be the last line\n";
        $content .= "Route::varbox();\n";

        $this->files->put($this->getRoutesFile(), $content);
    }

    /**
     * @return void
     */
    protected function createController()
    {
        $directory = app_path('Http/Controllers/Admin');

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true, true);
        }

        $this->files->put($this->getControllerFile(), $this->buildController());
    }









    /**
     * Get the block's composer contents.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildRoutes()
    {
        $draftContent = $revisionContent = $duplicateContent = $previewContent = $orderContent = '';

        $beginContent = $this->replaceDummyContent(
            $this->files->get($this->getRoutesBeginStub())
        );

        if ($this->useDraft) {
            $draftContent = $this->replaceDummyContent(
                $this->files->get($this->getRoutesDraftStub())
            );
        }

        if ($this->useRevisions) {
            $revisionContent = $this->replaceDummyContent(
                $this->files->get($this->getRoutesRevisionStub())
            );
        }

        if ($this->useDuplicate) {
            $duplicateContent = $this->replaceDummyContent(
                $this->files->get($this->getRoutesDuplicateStub())
            );
        }

        if ($this->usePreview) {
            $previewContent = $this->replaceDummyContent(
                $this->files->get($this->getRoutesPreviewStub())
            );
        }

        if ($this->useOrder) {
            $orderContent = $this->replaceDummyContent(
                $this->files->get($this->getRoutesOrderStub())
            );
        }

        $endContent = $this->replaceDummyContent(
            $this->files->get($this->getRoutesEndStub())
        );

        return implode('', [
            $beginContent, $draftContent, $revisionContent, $duplicateContent, $previewContent, $orderContent, $endContent
        ]);
    }

    /**
     * Get the block's composer contents.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildController()
    {
        $draftUseContent = $revisionUseContent = $duplicateUseContent = $previewUseContent = $orderUseContent = '';

        $beginContent = $this->replaceDummyContent(
            $this->files->get($this->getControllerBeginStub())
        );

        if ($this->useDraft) {
            $draftUseContent = $this->files->get($this->getControllerDraftUseStub());
        }

        if ($this->useRevisions) {
            $revisionUseContent = $this->files->get($this->getControllerRevisionUseStub());
        }

        if ($this->useDuplicate) {
            $duplicateUseContent = $this->files->get($this->getControllerDuplicateUseStub());
        }

        if ($this->usePreview) {
            $previewUseContent = $this->files->get($this->getControllerPreviewUseStub());
        }

        if ($this->useOrder) {
            $orderUseContent = $this->files->get($this->getControllerOrderUseStub());
        }

        $mainContent = $this->replaceDummyContent(
            $this->files->get($this->getControllerContentStub())
        );

        $endContent = $this->replaceDummyContent(
            $this->files->get($this->getControllerEndStub())
        );

        return implode('', [
            $beginContent,
            $draftUseContent, $revisionUseContent, $duplicateUseContent, $previewUseContent, $orderUseContent,
            $mainContent,
            $endContent
        ]);
    }








    /**
     * @param string $content
     * @return string|string[]
     */
    protected function replaceDummyContent($content)
    {
        $content = str_replace('DummySnakeName', "{$this->pluralSnakeModelName()}", $content);
        $content = str_replace('DummySlugName', "{$this->pluralSlugModelName()}", $content);
        $content = str_replace('DummyPluralName', "{$this->pluralNormalModelName()}", $content);
        $content = str_replace('DummySingularName', "{$this->normalModelName()}", $content);
        $content = str_replace('DummyControllerNamespace', "{$this->controllerNamespace()}", $content);
        $content = str_replace('DummyFullModelNamespace', "{$this->fullModelNamespace()}", $content);
        $content = str_replace('DummyModelName', "{$this->camelModelName()}", $content);
        $content = str_replace('DummyLastModelSegment', "{$this->lastModelSegment()}", $content);
        $content = str_replace('DummyViewsPath', "{$this->dotViewsPath()}", $content);

        return $content;
    }








    /**
     * @return string
     */
    protected function getRoutesFile()
    {
        return base_path('routes/web.php');
    }

    /**
     * @return string
     */
    protected function getControllerFile()
    {
        return app_path('Http/Controllers/Admin/' . $this->controllerNamespace() . '.php');
    }










    /**
     * @return string
     */
    protected function getRoutesBeginStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/begin.stub';
    }

    /**
     * @return string
     */
    protected function getRoutesEndStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/end.stub';
    }

    /**
     * @return string
     */
    protected function getRoutesDraftStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/draft.stub';
    }

    /**
     * @return string
     */
    protected function getRoutesRevisionStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/revision.stub';
    }

    /**
     * @return string
     */
    protected function getRoutesDuplicateStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/duplicate.stub';
    }

    /**
     * @return string
     */
    protected function getRoutesPreviewStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/preview.stub';
    }

    /**
     * @return string
     */
    protected function getRoutesOrderStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/order.stub';
    }








    /**
     * @return string
     */
    protected function getControllerBeginStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/begin.stub';
    }

    /**
     * @return string
     */
    protected function getControllerContentStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/content.stub';
    }

    /**
     * @return string
     */
    protected function getControllerEndStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/end.stub';
    }

    /**
     * @return string
     */
    protected function getControllerDraftUseStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/draft_import.stub';
    }

    /**
     * @return string
     */
    protected function getControllerRevisionUseStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/revision_import.stub';
    }

    /**
     * @return string
     */
    protected function getControllerDuplicateUseStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/duplicate_import.stub';
    }

    /**
     * @return string
     */
    protected function getControllerPreviewUseStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/preview_import.stub';
    }

    /**
     * @return string
     */
    protected function getControllerOrderUseStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/order_import.stub';
    }







    /**
     * @return string
     */
    protected function fullModelNamespace()
    {
        return $this->qualifyClass($this->modelClass);
    }

    /**
     * @return string
     */
    protected function lastModelSegment()
    {
        return Arr::last(explode('\\', $this->fullModelNamespace()));
    }

    /**
     * @return string
     */
    protected function pluralSnakeModelName()
    {
        return Str::plural(Str::snake($this->lastModelSegment()));
    }

    /**
     * @return string
     */
    protected function pluralSlugModelName()
    {
        return Str::plural(Str::slug(Str::snake($this->lastModelSegment())));
    }

    /**
     * @return string
     */
    protected function normalModelName()
    {
        return Str::title($this->lastModelSegment());
    }

    /**
     * @return string
     */
    protected function pluralNormalModelName()
    {
        return Str::plural($this->normalModelName());
    }

    /**
     * @return string
     */
    protected function camelModelName()
    {
        return Str::camel($this->lastModelSegment());
    }

    /**
     * @return string
     */
    protected function pluralModelName()
    {
        return Str::plural($this->lastModelSegment());
    }





    /**
     * @return string
     */
    protected function controllerNamespace()
    {
        return "{$this->pluralModelName()}Controller";
    }

    /**
     * @return string
     */
    protected function fullControllerNamespace()
    {
        return "{$this->laravel->getNamespace()}Http\Controllers\Admin\\{$this->controllerNamespace()}";
    }






    /**
     * @return string|string[]
     */
    protected function dotViewsPath()
    {
        return str_replace('/', '.', $this->viewsPath);
    }







    /**
     * Ask for the fully qualified namespace of the model to be created.
     *
     * @return string|null
     */
    protected function askForModelClass()
    {
        if ($this->option('no-interaction') == true) {
            return null;
        }

        $question = [];
        $question[] = 'What will be the model\'s fully qualified namespace?';
        $question[] = ' <fg=white>Example: <fg=yellow>App\Models\Post</></>';

        return trim($this->ask(implode(PHP_EOL, $question)));
    }

    /**
     * Ask for the fully qualified namespace of the model to be created.
     *
     * @return string|null
     */
    protected function askForViewsPath()
    {
        if ($this->option('no-interaction') == true) {
            return null;
        }

        $question = [];
        $question[] = 'Where would you like to store the admin blade views?';
        $question[] = ' <fg=white>Please provide a path relative to: <fg=yellow>/resources/views</></>';
        $question[] = ' <fg=white>Example: <fg=yellow>admin/posts</></>';

        $answer = trim($this->ask(implode(PHP_EOL, $question)));

        return $answer ? resource_path('views/' . $answer) : null;
    }

    /**
     * Ask if the draft functionality should be enabled.
     *
     * @return string|null
     */
    protected function askForUsingDraft()
    {
        if ($this->option('no-interaction') == true) {
            return false;
        }

        $answer = $this->choice('Would you like to enable the <fg=red>draft</> functionality?', [
            true => 'yes', false => 'no'
        ], 'no');

        return $answer == 'yes' ? true : false;
    }

    /**
     * Ask if the revision functionality should be enabled.
     *
     * @return string|null
     */
    protected function askForUsingRevisions()
    {
        if ($this->option('no-interaction') == true) {
            return false;
        }

        $answer = $this->choice('Would you like to enable the <fg=red>revision</> functionality?', [
            true => 'yes', false => 'no'
        ], 'no');

        return $answer == 'yes' ? true : false;
    }

    /**
     * Ask if the duplicate functionality should be enabled.
     *
     * @return string|null
     */
    protected function askForUsingDuplicate()
    {
        if ($this->option('no-interaction') == true) {
            return false;
        }

        $answer = $this->choice('Would you like to enable the <fg=red>duplicate</> functionality?', [
            true => 'yes', false => 'no'
        ], 'no');

        return $answer == 'yes' ? true : false;
    }

    /**
     * Ask if the preview functionality should be enabled.
     *
     * @return string|null
     */
    protected function askForUsingPreview()
    {
        if ($this->option('no-interaction') == true) {
            return false;
        }

        $answer = $this->choice('Would you like to enable the <fg=red>preview</> functionality?', [
            true => 'yes', false => 'no'
        ], 'no');

        return $answer == 'yes' ? true : false;
    }

    /**
     * Ask if the order functionality should be enabled.
     *
     * @return string|null
     */
    protected function askForUsingOrder()
    {
        if ($this->option('no-interaction') == true) {
            return false;
        }

        $answer = $this->choice('Would you like to enable the <fg=red>order</> functionality?', [
            true => 'yes', false => 'no'
        ], 'no');

        return $answer == 'yes' ? true : false;
    }








    /**
     * Get the stub file for the generator.
     *
     * @return void
     */
    protected function getStub() {}
}
