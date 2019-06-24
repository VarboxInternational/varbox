<?php

namespace Varbox\Helpers;

use Collective\Html\FormFacade;
use Illuminate\Support\Collection;
use Varbox\Contracts\AdminFormHelperContract;

class AdminFormHelper implements AdminFormHelperContract
{
    /**
     * The instance of the form facade.
     *
     * @var FormFacade
     */
    protected $form;

    /**
     * The current model instance for the form.
     *
     * @var mixed
     */
    protected $model;

    /**
     * Whether or not the form element should be wrapped inside div.form-group.
     *
     * @var bool
     */
    protected $wrap = true;

    /**
     * @set form
     */
    public function __construct()
    {
        $this->form = FormFacade::getFacadeRoot();
    }

    /**
     * If an unknown method has been invoked, call the method on the Collective\Html\FormFacade.
     * If event the facade does not have that method, than it's __call() will be invoked.
     *
     * @param string $method
     * @param array|null $arguments
     * @return mixed
     */
    public function __call($method, $arguments = null)
    {
        return call_user_func_array([$this->form, $method], $arguments);
    }

    /**
     * Wrap the form element.
     *
     * @return $this
     */
    public function yesWrap()
    {
        $this->wrap = true;

        return $this;
    }

    /**
     * Don't wrap the form element.
     *
     * @return $this
     */
    public function noWrap()
    {
        $this->wrap = false;

        return $this;
    }

    /**
     * Wraps the input field into html to match the admin layout.
     *
     * @param string $input
     * @param string $label
     * @return string
     */
    public function wrap($input, $label)
    {
        return $this->wrap ?
            '<div class="form-group">' . $label . $input . '</div>' :
            $label . $input;
    }

    /**
     * Create a new model based form builder.
     *
     * @param mixed $model
     * @param array $options
     * @return string
     */
    public function model($model, array $options = [])
    {
        $this->model = $model;

        $this->form->setModel($model);

        return $this->open($options);
    }

    /**
     * Open up a new HTML form.
     *
     * @param array $options
     * @return string
     */
    public function open(array $options = [])
    {
        return $this->form->open($options);
    }

    /**
     * Close the current form.
     *
     * @return string
     */
    public function close()
    {
        return $this->form->close();
    }

    /**
     * Create a submit button element.
     *
     * @param  string $value
     * @param array $options
     * @return string
     */
    public function submit($value = null, array $options = [])
    {
        return $this->form->submit($value, $options);
    }

    /**
     * Create a reset button element.
     *
     * @param  string $value
     * @param array $options
     * @return string
     */
    public function reset($value = null, array $options = [])
    {
        return $this->form->reset($value, $options);
    }

    /**
     * Create a button element.
     *
     * @param  string $value
     * @param array $options
     * @return string
     */
    public function button($value = null, array $options = [])
    {
        return $this->form->button($value, $options);
    }

    /**
     * Create a hidden input field.
     *
     * @param string  $name
     * @param string  $value
     * @param array   $options
     * @return string
     */
    public function hidden($name, $value = null, array $options = [])
    {
        $options['data-value'] = $value;
        $options['id'] = $options['id'] ?? $name . '-input';

        return $this->form->hidden($this->name($name), $this->value($name, $value), $this->options($options));
    }

    /**
     * Create a text input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function text($name, $label = null, $value = null, array $options = [])
    {
        $options['data-value'] = $value;
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'form-control ' . ($options['class'] ?? '');

        return $this->wrap(
            $this->form->text($this->name($name), $this->value($name, $value), $this->options($options)),
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a textarea input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function textarea($name, $label = null, $value = null, array $options = [])
    {
        $options['data-value'] = $value;
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'form-control ' . ($options['class'] ?? '');

        return $this->wrap(
            $this->form->textarea($this->name($name), $this->value($name, $value), $this->options($options)),
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a select input field.
     *
     * @param string $name
     * @param string|null $label
     * @param array $list
     * @param string|null $selected
     * @param array $options
     * @return string
     */
    public function select($name, $label = null, $list = [], $selected = null, array $options = [])
    {
        $list = $list instanceof Collection ? $list->toArray() : $list;
        $selected = $selected instanceof Collection ? $selected->toArray() : $selected;

        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'form-control select-input ' . ($options['class'] ?? '');
        $options['data-selected'] = is_array($selected) ? json_encode($selected) : $selected;

        return $this->wrap(
            $this->form->select($this->name($name), $list, $this->value($name, $selected), $this->options($options)),
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a password input field.
     *
     * @param string $name
     * @param string|null $label
     * @param array $options
     * @param bool $generate
     * @return string
     */
    public function password($name, $label = null, array $options = [], $generate = false)
    {
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'form-control ' . ($options['class'] ?? '');

        if ($generate) {
            return $this->wrap(
                '<span class="input-group-append">' .
                $this->form->password($this->name($name), $this->options($options)) .
                '<span class="input-group-append"><button class="password-generate btn btn-primary btn-square" type="button">Generate</button></span></span>',
                $this->label($name, $label, $options)
            );
        } else {
            return $this->wrap($this->form->password($this->name($name), $this->options($options)), $this->label($name, $label, $options));
        }
    }

    /**
     * Create a file input field.
     *
     * @param string $name
     * @param string|null $label
     * @param array $options
     * @return string
     */
    public function file($name, $label = null, array $options = [])
    {
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'custom-file-input ' . ($options['class'] ?? '');

        return $this->wrap(
            '<div class="custom-file">' . $this->form->file($this->name($name), $this->options($options)) . '<label class="custom-file-label">Choose file</label></div>',
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a number input field.
     *
     * @param string $name
     * @param string|null $value
     * @param string|null $label
     * @param array $options
     * @return string
     */
    public function number($name, $label = null, $value = null, array $options = [])
    {
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'form-control ' . ($options['class'] ?? '');
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->number($this->name($name), $this->value($name, $value), $this->options($options)),
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a checkbox input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $subLabel
     * @param bool|null $checked
     * @param int|null|string $value
     * @param array $options
     * @return string
     */
    public function checkbox($name, $label = null, $subLabel = null, $value = 1, $checked = null, array $options = [])
    {
        $options['data-value'] = $value;
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'custom-control-input ' . ($options['class'] ?? '');

        return $this->wrap(
            '<label class="custom-control custom-checkbox">' .
            $this->form->checkbox($this->name($name), $this->value($name, $value), $checked, $this->options($options)) .
            '<span class="custom-control-label">' . $subLabel . '</span></label>',
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a radio input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $subLabel
     * @param bool|null $checked
     * @param int|null|string $value
     * @param array $options
     * @return string
     */
    public function radio($name, $label = null, $subLabel = null, $value = 1, $checked = null, array $options = [])
    {
        $options['data-value'] = $value;
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'custom-control-input ' . ($options['class'] ?? '');

        return $this->wrap(
            '<label class="custom-control custom-radio">' .
            $this->form->radio($this->name($name), $this->value($name, $value), $checked, $this->options($options)) .
            '<div class="custom-control-label">' . $subLabel . '</div></label>',
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a yes/no input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $subLabel
     * @param bool|null $checked
     * @param int|null|string $value
     * @param array $options
     * @return string
     */
    public function yesno($name, $label = null, $subLabel = null, $value = 1, $checked = null, array $options = [])
    {
        $options['data-value'] = $value;
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'custom-switch-input ' . ($options['class'] ?? '');

        return $this->wrap(
            '<label class="custom-switch">' .
            $this->form->checkbox($this->name($name), $this->value($name, $value), $checked, $this->options($options)) .
            '<span class="custom-switch-indicator"></span>' .
            '<span class="custom-switch-description">' . $subLabel . '</span></label>',
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a date input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function date($name, $label = null, $value = null, array $options = [])
    {
        $options['data-value'] = $value;
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'form-control ' . ($options['class'] ?? '');
        $options['data-mask'] = '0000-00-00';
        $options['data-mask-clearifnotmatch'] = 'true';
        $options['placeholder'] = 'yyyy-mm-dd';

        return $this->wrap(
            $this->form->text($this->name($name), $this->value($name, $value), $this->options($options)),
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a time input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function time($name, $label = null, $value = null, array $options = [])
    {
        $options['data-value'] = $value;
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'form-control ' . ($options['class'] ?? '');
        $options['data-mask'] = '00:00';
        $options['data-mask-clearifnotmatch'] = 'true';
        $options['placeholder'] = 'hh:mm';

        return $this->wrap(
            $this->form->text($this->name($name), $this->value($name, $value), $this->options($options)),
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a datetime input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function datetime($name, $label = null, $value = null, array $options = [])
    {
        $options['data-value'] = $value;
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'form-control ' . ($options['class'] ?? '');
        $options['data-mask'] = '0000-00-00 00:00';
        $options['data-mask-clearifnotmatch'] = 'true';
        $options['placeholder'] = 'yyyy-mm-dd hh:mm';

        return $this->wrap(
            $this->form->text($this->name($name), $this->value($name, $value), $this->options($options)),
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create an editor field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function editor($name, $label = null, $value = null, array $options = [])
    {
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'editor-input ' . ($options['class'] ?? '');

        return $this->wrap(
            $this->form->textarea($this->name($name), $this->value($name, $value), $this->options($options)),
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a calendar input field.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function calendar($name, $label = null, $value = null, array $options = [])
    {
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'date-input ' . ($options['class'] ?? '');
        $options['data-value'] = $value;

        return $this->wrap(
            $this->form->text($this->name($name), $this->value($name, $value), $this->options($options)),
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a select input field for a range.
     *
     * @param string $name
     * @param string|null $label
     * @param int $start
     * @param int $end
     * @param string|null $selected
     * @param array $options
     * @return string
     */
    public function selectRange($name, $label = null, $start = 0, $end = 0, $selected = null, array $options = [])
    {
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'select-input ' . ($options['class'] ?? '');
        $options['data-selected'] = $selected;

        return $this->wrap(
            $this->form->selectRange($this->name($name), $start, $end, $this->value($name, $selected), $this->options($options)),
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a select input field for years.
     *
     * @param string $name
     * @param string|null $label
     * @param int $start
     * @param int $end
     * @param string|null $selected
     * @param array $options
     * @return string
     */
    public function selectYear($name, $label = null, $start = null, $end = null, $selected = null, array $options = [])
    {
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'form-control select-input ' . ($options['class'] ?? '');
        $options['data-selected'] = is_array($selected) ? json_encode($selected) : $selected;

        return $this->wrap(
            $this->form->selectYear($this->name($name), $start ?: 1970, $end ?: date('Y'), $this->value($name, $selected), $this->options($options)),
            $this->label($name, $label, $options)
        );
    }

    /**
     * Create a select input field for months.
     *
     * @param string $name
     * @param string|null $label
     * @param string|null $selected
     * @param array $options
     * @param string $format
     * @return string
     */
    public function selectMonth($name, $label = null, $selected = null, array $options = [], $format = '%B')
    {
        $options['id'] = $options['id'] ?? $name . '-input';
        $options['class'] = 'select-input ' . ($options['class'] ?? '');
        $options['data-selected'] = $selected;

        return $this->wrap(
            $this->form->selectMonth($this->name($name), $this->value($name, $selected), $this->options($options), $format),
            $this->label($name, $label, $options)
        );
    }

    /**
     * Set the name of the field.
     *
     * @param string $name
     * @return mixed
     */
    protected function name($name)
    {
        return $name;
    }

    /**
     * Set the value of the field.
     *
     * @param string $name
     * @param string $value
     * @return mixed
     */
    protected function value($name, $value = null)
    {
        return $value;
    }

    /**
     * Set the label using the name if no label was specified.
     * Specify the label to false to render only the input, without any wrappings.
     *
     * @param string $name
     * @param null $label
     * @param array $options
     * @return string
     */
    protected function label($name, $label = null, $options = [])
    {
        if ($label === false) {
            return '';
        }

        $_label = [];

        $_label[] = '<label class="form-label" for="' . (empty($options['id']) ? $name . '-input' : $options['id']) . '">';
        $_label[] = $label ?: ucfirst(preg_replace("/[^a-zA-Z0-9\s]/", " ", $name));

        if (in_array('required', $options) || array_key_exists('required', $options)) {
            $_label[] = '<span class="form-required">*</span>';
        }

        $_label[] = '</label>';

        return implode('', $_label);
    }

    /**
     * Sanitize the attributes for a field.
     *
     * @param array $options
     * @return array
     */
    protected function options($options = [])
    {
        foreach ($options as $key => $val) {
            if ($key == 'required' || $val == 'required') {
                unset($options[$key]);
            }
        }

        return $options;
    }
}
