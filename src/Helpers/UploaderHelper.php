<?php

namespace Varbox\Helpers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Varbox\Contracts\UploadedHelperContract;
use Varbox\Contracts\UploaderHelperContract;
use Varbox\Exceptions\UploadException;
use Varbox\Models\Upload;

class UploaderHelper implements UploaderHelperContract
{
    /**
     * The view container instance.
     *
     * @var View
     */
    protected $view;

    /**
     * The field of the uploader input.
     * Generally this represents a field in the database that holds or can hold a path for an uploaded file.
     *
     * @var string
     */
    protected $field;

    /**
     * The label that comes along with the generated field.
     *
     * @var string
     */
    protected $label;

    /**
     * The loaded or unloaded model class on which the uploader manager will function.
     *
     * @var Model
     */
    protected $model;

    /**
     * The current uploaded file.
     *
     * @var UploadedHelperContract|null
     */
    protected $current;

    /**
     * The extensions accepted for an upload as array.
     *
     * @var array
     */
    protected $accept = [];

    /**
     * Flag indicating whether the upload manager should be disabled or not.
     *
     * @var bool
     */
    protected $disabled = false;

    /**
     * The styles a file can have.
     * Mainly, this applies to images and videos only.
     * For any other file type, the original should suffice.
     *
     * @var array
     */
    protected $styles = [
        'original'
    ];

    /**
     * The type of files that can be uploaded.
     * These types will be separated in tabs on the uploader popup.
     * Accepted values: image | video | audio | file
     *
     * @var array
     */
    protected $types = [
        'image',
        'video',
        'audio',
        'file'
    ];

    /**
     * The default values for styles, types and accept.
     * This is used to re-initialize these properties after the uploader finished rendering.
     * This way, the next uploader instance on page, won't inherit values from the previous one.
     *
     * @var array
     */
    private $defaults = [
        'field' => null,
        'label' => null,
        'model' => null,
        'current' => null,
        'accept' => [],
        'disabled' => false,
        'styles' => [
            'original'
        ],
        'types' => [
            'image',
            'video',
            'audio',
            'file'
        ],
    ];

    /**
     * The index used to identify an existing uploader number.
     *
     * @var int
     */
    private $i = 0;

    /**
     * Build and render the uploader form.
     *
     * @return View
     */
    public function manager()
    {
        $this->checkModel()->checkField();
        $this->parseLabel()->parseTypes()->parseAccept();
        $this->generateCurrent()->generateStyles();
        $this->buildManagerView()->resetToDefaults();

        $this->i++;

        return $this->view;
    }

    /**
     * Set or get the name of an uploader instance.
     *
     * @param string|null $field
     * @return $this|string
     */
    public function field($field = null)
    {
        if ($field === null) {
            return $this->field;
        }

        $this->field = $field;

        return $this;
    }

    /**
     * Set or get the label for an uploader instance.
     *
     * @param string|null $label
     * @return $this|string
     */
    public function label($label = null)
    {
        if ($label === null) {
            return $this->label;
        }

        $this->label = $label;

        return $this;
    }

    /**
     * Set or get the model for an uploader instance.
     *
     * @param Model|null $model
     * @return $this|string
     */
    public function model(Model $model = null)
    {
        if ($model === null) {
            return $this->model;
        }

        $this->model = $model;

        return $this;
    }

    /**
     * Set or get the types for an uploader instance.
     *
     * @param array|string $types
     * @return $this|array
     */
    public function types(...$types)
    {
        if (!$types) {
            return $this->types;
        }

        $this->types = $types;

        return $this;
    }

    /**
     * Set or get the accepted extensions for an uploader instance.
     *
     * @param array|string $accept
     * @return $this|array
     */
    public function accept(...$accept)
    {
        if (!$accept) {
            return $this->accept;
        }

        $this->accept = $accept;

        return $this;
    }

    /**
     * Set the $disabled property to true.
     * This means that the current uploader instance will be disabled.
     * No upload or crop will be available, just viewing the existing uploaded file.
     *
     * @return $this
     */
    public function disabled()
    {
        $this->disabled = true;

        return $this;
    }

    /**
     * Build the helpers.uploader.manager view with the generated properties.
     *
     * @return $this
     */
    protected function buildManagerView()
    {
        $this->view = view('varbox::helpers.uploader.manager')->with([
            'i' => $this->i,
            'index' => rand(1, 999999),
            'field' => $this->field,
            'label' => $this->label,
            'model' => $this->model,
            'current' => $this->current,
            'upload' => $this->current ? Upload::whereFullPath($this->current->getFile())->first() : null,
            'styles' => $this->styles,
            'types' => $this->types,
            'accept' => $this->accept,
            'disabled' => $this->disabled,
        ]);

        return $this;
    }

    /**
     * Reset the properties to their default value.
     * This way, the next uploader helper instance in the page won't inherit values from the previous one.
     *
     * @return $this
     */
    protected function resetToDefaults()
    {
        $this->field = $this->defaults['field'];
        $this->label = $this->defaults['label'];
        $this->model = $this->defaults['model'];
        $this->current = $this->defaults['current'];
        $this->styles = $this->defaults['styles'];
        $this->types = $this->defaults['types'];
        $this->accept = $this->defaults['accept'];
        $this->disabled = $this->defaults['disabled'];

        return $this;
    }

    /**
     * Check if the uploader instance already has a current upload and set it.
     *
     * @return $this
     */
    protected function generateCurrent()
    {
        if (!$this->model->exists) {
            return $this;
        }

        if (Str::contains($this->field, '[') && Str::contains($this->field, ']')) {
            $attribute = strtok($this->field, '[');
            $file = Arr::get(
                get_object_vars_recursive($this->model->{$attribute}),
                str_replace('][', '.', trim(str_replace($attribute, '', $this->field), '.[]'))
            );

            $upload = uploaded($file);

            $this->current = $upload->exists() ? $upload : null;

            return $this;
        }

        $upload = uploaded($this->model->{$this->field});

        $this->current = $upload->exists() ? $upload : null;

        return $this;
    }

    /**
     * Set the styles of the current field referenced by the uploaded file.
     * If no styles are defined by the model, set the styles property to its default value.
     *
     * @return $this
     */
    protected function generateStyles()
    {
        if (
            method_exists($this->model, 'getUploadConfig') &&
            ($styles = array_search_key_recursive($this->field, $this->model->getUploadConfig(), true))
        ) {
            $this->styles = array_keys($styles);
        } else {
            $this->styles = $this->defaults['styles'];
        }

        return $this;
    }

    /**
     * Create the label if it was not passed as an argument.
     *
     * @return $this
     */
    protected function parseLabel()
    {
        if (!$this->label) {
            $this->label = Str::title(str_replace('_', ' ', $this->field));
        }

        return $this;
    }

    /**
     * Clean the given types parameter if the developer passed wrong data.
     * Remove additional unwanted types.
     *
     * @return $this
     */
    protected function parseTypes()
    {
        foreach ($this->types as $index => $type) {
            if (!in_array($type, ['image', 'video', 'audio', 'file'])) {
                unset($this->types[$index]);
            }
        }

        return $this;
    }

    /**
     * Clean the given accept parameter if the developer passed wrong data.
     * Refactor the accept parameter if it contains an "all" (*) attribute.
     *
     * @return $this
     */
    protected function parseAccept()
    {
        if (count($this->accept) > 0 && in_array('*', $this->accept)) {
            $this->accept = [];
        }

        return $this;
    }

    /**
     * Check if a model instance was passed to the uploader helper.
     * The model must be of type App\Models\Model.
     *
     * @return $this
     */
    protected function checkModel()
    {
        if (!$this->model) {
            throw UploadException::invalidUploaderModel();
        }

        return $this;
    }

    /**
     * Check if field name was passed to the uploader helper.
     * The field must be a string representing an existing column on the model's database table.
     *
     * @return $this
     */
    protected function checkField()
    {
        if (!$this->field) {
            throw UploadException::invalidUploaderField();
        }

        return $this;
    }
}
