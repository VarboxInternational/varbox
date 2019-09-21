<?php

namespace Varbox\Commands;

use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CrudMakeCommand extends GeneratorCommand
{
    use DetectsApplicationNamespace;

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
     */
    public function handle()
    {
        $this->askQuestions();
        $this->guardAgainstExistingFiles();

        $this->createRoutes();
        $this->createController();

        dd($this->useDraft, $this->useRevisions, $this->useDuplicate, $this->usePreview);


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

        if (!$this->modelClass || !Str::startsWith($this->modelClass, $this->getAppNamespace())) {
            $this->error('Invalid model FQN provided');
            $this->line('It should start with: <fg=yellow>' . $this->getAppNamespace());
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
        $content = str_replace('Route::url();', '', $content);

        $content .= $this->buildRoutes();
        $content .= "\n// This should be the last line\n";
        $content .= "Route::url();\n";

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
        $beginContent = $this->replaceDummyContent(
            $this->files->get($this->getRoutesBeginStub())
        );

        if ($this->useDraft) {
            $draftContent = $this->replaceDummyContent(
                $this->files->get($this->getRoutesDraftStub())
            );
        } else {
            $draftContent = '';
        }

        if ($this->useRevisions) {
            $revisionContent = $this->replaceDummyContent(
                $this->files->get($this->getRoutesRevisionStub())
            );
        } else {
            $revisionContent = '';
        }

        if ($this->useDuplicate) {
            $duplicateContent = $this->replaceDummyContent(
                $this->files->get($this->getRoutesDuplicateStub())
            );
        } else {
            $duplicateContent = '';
        }

        if ($this->usePreview) {
            $previewContent = $this->replaceDummyContent(
                $this->files->get($this->getRoutesPreviewStub())
            );
        } else {
            $previewContent = '';
        }

        $endContent = $this->replaceDummyContent(
            $this->files->get($this->getRoutesEndStub())
        );

        return implode('', [
            $beginContent, $draftContent, $revisionContent, $duplicateContent, $previewContent, $endContent
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
        $beginContent = $this->replaceDummyContent(
            $this->files->get($this->getControllerBeginStub())
        );

        if ($this->useDraft) {
            $draftUseContent = $this->files->get($this->getControllerDraftUseStub());
        } else {
            $draftUseContent = '';
        }

        if ($this->useRevisions) {
            $revisionUseContent = $this->files->get($this->getControllerRevisionUseStub());
        } else {
            $revisionUseContent = '';
        }

        if ($this->useDuplicate) {
            $duplicateUseContent = $this->files->get($this->getControllerDuplicateUseStub());
        } else {
            $duplicateUseContent = '';
        }

        if ($this->usePreview) {
            $previewUseContent = $this->files->get($this->getControllerPreviewUseStub());
        } else {
            $previewUseContent = '';
        }

        $mainContent = $this->replaceDummyContent(
            $this->files->get($this->getControllerContentStub())
        );

        $endContent = $this->replaceDummyContent(
            $this->files->get($this->getControllerEndStub())
        );

        return implode('', [
            $beginContent,
            $draftUseContent, $revisionUseContent, $duplicateUseContent, $previewUseContent,
            $endContent
        ]);
    }

    protected function replaceDummyContent($content)
    {
        $content = str_replace('DummySnakeName', "{$this->pluralSnakeModelName()}", $content);
        $content = str_replace('DummySlugName', "{$this->pluralSlugModelName()}", $content);
        $content = str_replace('DummyControllerNamespace', "{$this->controllerNamespace()}", $content);
        $content = str_replace('DummyModelNamespace', "{$this->controllerNamespace()}", $content);
        $content = str_replace('DummyModelName', "{$this->camelModelName()}", $content);

        return $content;
    }

    protected function getRoutesFile()
    {
        return base_path('routes/web.php');
    }

    protected function getControllerFile()
    {
        return app_path('Http/Controllers/Admin/' . $this->controllerNamespace() . '.php');
    }

    protected function getRoutesBeginStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/begin.stub';
    }

    protected function getRoutesEndStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/end.stub';
    }

    protected function getRoutesDraftStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/draft.stub';
    }

    protected function getRoutesRevisionStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/revision.stub';
    }

    protected function getRoutesDuplicateStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/duplicate.stub';
    }

    protected function getRoutesPreviewStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/routes/preview.stub';
    }

    protected function getControllerBeginStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/begin.stub';
    }

    protected function getControllerContentStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/content.stub';
    }

    protected function getControllerEndStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/end.stub';
    }

    protected function getControllerDraftUseStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/draft_import.stub';
    }

    protected function getControllerRevisionUseStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/revision_import.stub';
    }

    protected function getControllerDuplicateUseStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/duplicate_import.stub';
    }

    protected function getControllerPreviewUseStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/crud/controller/preview_import.stub';
    }

    protected function getLastModelSegment()
    {
        return Arr::last(explode('\\', $this->qualifyClass($this->modelClass)));
    }

    protected function pluralSnakeModelName()
    {
        return Str::plural(Str::snake($this->getLastModelSegment()));
    }

    protected function pluralSlugModelName()
    {
        return Str::plural(Str::slug(Str::snake($this->getLastModelSegment())));
    }

    protected function camelModelName()
    {
        return Str::camel($this->getLastModelSegment());
    }

    protected function pluralModelName()
    {
        return Str::plural($this->getLastModelSegment());
    }

    protected function controllerNamespace()
    {
        return "{$this->pluralModelName()}Controller";
    }

    protected function fullControllerNamespace()
    {
        return "{$this->getAppNamespace()}Http\Controllers\Admin\\{$this->controllerNamespace()}";
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
     * Get the stub file for the generator.
     *
     * @return void
     */
    protected function getStub() {}
}
