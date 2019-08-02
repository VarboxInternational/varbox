<?php

namespace Varbox\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Varbox\Contracts\EmailModelContract;

class MailMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'varbox:make-mail 
                            {type : The type of the class. Can be any key from the "config/varbox/emails.php" -> "types" config option}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the necessary files for a VarBox email.';

    /**
     * The filesystem instance.
     *
     * @var Filesystem
     */
    protected $files;

    /**
     * The email model instance.
     *
     * @var EmailModelContract
     */
    protected $model;

    /**
     * All email types defined in the "config/varbox/emails.php" -> "types".
     *
     * @var array
     */
    protected $types = [];

    /**
     * The type of the class from "config/varbox/emails.php" -> "types".
     *
     * @var string
     */
    protected $type;

    /**
     * The mailable class.
     *
     * @var string
     */
    protected $class;

    /**
     * The mailable view.
     *
     * @var string
     */
    protected $view;

    /**
     * The mailable class path name.
     *
     * @var string
     */
    protected $classPath;

    /**
     * The mailable view path name.
     *
     * @var string
     */
    protected $viewPath;

    /**
     * Flag indicating if the mail should use queues.
     *
     * @var bool
     */
    protected $useQueue;

    /**
     * Create a new command instance.
     *
     * return void
     * @param Filesystem $files
     * @param EmailModelContract $model
     */
    public function __construct(Filesystem $files, EmailModelContract $model)
    {
        parent::__construct();

        $this->files = $files;
        $this->model = $model;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $this->type = $this->argument('type');
        $this->types = $this->model->getTypes();

        if (!$this->isValidEmailType()) {
            return $this->invalidEmailTypeError();
        }

        if (!$this->hasValidClass()) {
            return $this->invalidMailableClassError();
        }

        if (!$this->hasValidView()) {
            return $this->invalidMailableViewError();
        }

        $this->class = $this->types[$this->type]['class'];
        $this->view = $this->types[$this->type]['view'];

        $this->establishPaths();

        if ($this->classAlreadyExists()) {
            return $this->classAlreadyExistsError();
        }

        if ($this->viewAlreadyExists()) {
            return $this->viewAlreadyExistsError();
        }

        $this->makeDirectory();

        $this->files->put($this->classPath, $this->buildClass());
        $this->files->put($this->viewPath, $this->buildView());

        $this->info('Mailable created successfully!');

        return true;
    }

    /**
     * Verify if the given type is present inside the "config/varbox/emails.php" -> "types" config option.
     *
     * @return bool
     */
    protected function isValidEmailType()
    {
        return array_key_exists($this->type, $this->types);
    }

    /**
     * Verify if the given email type has a valid class inside the the "config/varbox/emails.php" -> "types.class" config option.
     *
     * @return bool
     */
    protected function hasValidClass()
    {
        return isset($this->types[$this->type]['class']) && !empty($this->types[$this->type]['class']);
    }

    /**
     * Verify if the given email type has a valid class inside the the "config/varbox/emails.php" -> "types.view" config option.
     *
     * @return bool
     */
    protected function hasValidView()
    {
        return isset($this->types[$this->type]['view']) && !empty($this->types[$this->type]['view']);
    }

    /**
     * Get the composer stub file for the generator.
     *
     * @return string
     */
    protected function getClassStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/emails/class.stub';
    }

    /**
     * Get the mailable view stub file for the generator.
     *
     * @return string
     */
    protected function getViewStub()
    {
        return __DIR__ . '/../../resources/stubs/commands/emails/view.stub';
    }

    /**
     * Get the mailable's class contents.
     *
     * @return mixed
     * @throws FileNotFoundException
     */
    protected function buildClass()
    {
        if ($this->option('no-interaction') == true) {
            $useQueue = false;
        } else {
            $useQueue = $this->choice('Do you want to make the mail queueable?', [
                true => 'yes', false => 'no'
            ], true);
        }

        $content = $this->files->get($this->getClassStub());
        $content = str_replace('DummyNamespace', trim(substr($this->class, 0, strrpos($this->class, '\\')), '\\'), $content);
        $content = str_replace('DummyName', substr($this->class, strrpos($this->class, '\\') + 1), $content);
        $content = str_replace('the-email-type', $this->type, $content);

        if ($useQueue == 'no') {
            $content = str_replace(' implements ShouldQueue', '', $content);
            $content = str_replace('Queueable, ', '', $content);
        }

        $variables = '';

        if (isset($this->types[$this->type]['variables']) && !empty($this->types[$this->type]['variables'])) {
            foreach ($this->types[$this->type]['variables'] as $variable) {
                $variables .= "            '" . $variable . "' => null,\n";
            }
        }

        if ($variables && !empty($variables)) {
            $content = str_replace('// the email variables', $variables, $content);
        }

        return $content;
    }

    /**
     * Get the mailable's view contents.
     *
     * @return mixed
     * @throws FileNotFoundException
     */
    protected function buildView()
    {
        return $this->files->get($this->getViewStub());
    }

    /**
     * Set the correct path for the mailable class and view.
     *
     * @return void
     */
    protected function establishPaths()
    {
        $this->classPath = str_replace($this->laravel->getNamespace(), '', $this->class);
        $this->classPath = $this->laravel['path'] . DIRECTORY_SEPARATOR . $this->classPath . '.php';
        $this->classPath = str_replace('\\', DIRECTORY_SEPARATOR, $this->classPath);

        $this->viewPath = str_replace('.', DIRECTORY_SEPARATOR, $this->view);
        $this->viewPath = $this->laravel['path.resources'] . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $this->viewPath . '.blade.php';
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @return void
     */
    protected function makeDirectory()
    {
        if (!$this->files->isDirectory(dirname($this->classPath))) {
            $this->files->makeDirectory(dirname($this->classPath), 0755, true, true);
        }

        if (!$this->files->isDirectory(dirname($this->viewPath))) {
            $this->files->makeDirectory(dirname($this->viewPath), 0755, true, true);
        }
    }

    /**
     * Determine if the mail files already exists.
     *
     * @return bool
     */
    protected function classAlreadyExists()
    {
        return $this->files->exists($this->classPath) || class_exists($this->class);
    }

    /**
     * Determine if the mail files already exists.
     *
     * @return bool
     */
    protected function viewAlreadyExists()
    {
        return $this->files->exists($this->viewPath);
    }

    /**
     * Return with error if the email type is invalid.
     *
     * @return bool
     */
    protected function invalidEmailTypeError()
    {
        $this->error('There is no email type called "' . $this->type . '".');
        $this->line(PHP_EOL . 'The available email types are:');

        foreach (array_keys($this->model->getTypes()) as $type) {
            $this->comment('<bg=yellow> </> ' .  $type);
        }

        return false;
    }

    /**
     * Return with error if the mailable class is invalid.
     *
     * @return bool
     */
    protected function invalidMailableClassError()
    {
        $this->error('The email of type "' . $this->type . '" has no valid class!');
        $this->comment('<bg=yellow> </> ' . 'Please add a valid class inside the "config/varbox/emails.php" -> "types.' . $this->type . '.class" config option.');

        return false;
    }

    /**
     * Return with error if the mailable view is invalid.
     *
     * @return bool
     */
    protected function invalidMailableViewError()
    {
        $this->error('The email of type "' . $this->type . '" has no valid view!');
        $this->comment('<bg=yellow> </> ' . 'Please add a valid view inside the "config/varbox/emails.php" -> "types.' . $this->type . '.view" config option.');

        return false;
    }

    /**
     * Return with error if the mailable class already exxists.
     *
     * @return bool
     */
    protected function classAlreadyExistsError()
    {
        $this->error('The mailable class for the "' . $this->type . '" email type already exists!');
        $this->comment('<bg=yellow> </> ' . $this->classPath);

        return false;
    }

    /**
     * Return with error if the mailable view already exists.
     *
     * @return bool
     */
    protected function viewAlreadyExistsError()
    {
        $this->error('The mailable view for the "' . $this->type . '" email type already exists!');
        $this->comment('<bg=yellow> </> ' . $this->viewPath);

        return false;
    }
}
