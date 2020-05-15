<?php

namespace Varbox\Contracts;

interface AdminFormHelperContract
{
    /**
     * @set form
     */
    public function __construct();

    /**
     * @param string $method
     * @param array|null $arguments
     * @return mixed
     */
    public function __call($method, $arguments = null);

    /**
     * @return $this
     */
    public function yesWrap();

    /**
     * @return $this
     */
    public function noWrap();

    /**
     * @param string $input
     * @param string $label
     * @return string
     */
    public function wrap($input, $label);

    /**
     * @param mixed $model
     * @param array $options
     * @return string
     */
    public function model($model, array $options = []);

    /**
     * @param array $options
     * @return string
     */
    public function open(array $options = []);

    /**
     * @return string
     */
    public function close();

    /**
     * @param  string $value
     * @param array $options
     * @return string
     */
    public function submit($value = null, array $options = []);

    /**
     * @param  string $value
     * @param array $options
     * @return string
     */
    public function reset($value = null, array $options = []);

    /**
     * @param  string $value
     * @param array $options
     * @return string
     */
    public function button($value = null, array $options = []);

    /**
     * @param string  $name
     * @param string  $value
     * @param array   $options
     * @return string
     */
    public function hidden($name, $value = null, array $options = []);

    /**
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function text($name, $label = null, $value = null, array $options = []);

    /**
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function textarea($name, $label = null, $value = null, array $options = []);

    /**
     * @param string $name
     * @param string|null $label
     * @param array $list
     * @param string|null $selected
     * @param array $options
     * @return string
     */
    public function select($name, $label = null, $list = [], $selected = null, array $options = []);

    /**
     * @param string $name
     * @param string|null $label
     * @param array $options
     * @param bool $generate
     * @return string
     */
    public function password($name, $label = null, array $options = [], $generate = false);

    /**
     * @param string $name
     * @param string|null $label
     * @param array $options
     * @return string
     */
    public function file($name, $label = null, array $options = []);

    /**
     * @param string $name
     * @param string|null $value
     * @param string|null $label
     * @param array $options
     * @return string
     */
    public function number($name, $label = null, $value = null, array $options = []);

    /**
     * @param string $name
     * @param string|null $label
     * @param string|null $subLabel
     * @param bool|null $checked
     * @param int|null|string $value
     * @param array $options
     * @return string
     */
    public function checkbox($name, $label = null, $subLabel = null, $value = 1, $checked = null, array $options = []);

    /**
     * @param string $name
     * @param string|null $label
     * @param string|null $subLabel
     * @param bool|null $checked
     * @param int|null|string $value
     * @param array $options
     * @return string
     */
    public function radio($name, $label = null, $subLabel = null, $value = 1, $checked = null, array $options = []);

    /**
     * @param string $name
     * @param string|null $label
     * @param string|null $subLabel
     * @param bool|null $checked
     * @param int|null|string $value
     * @param array $options
     * @return string
     */
    public function yesno($name, $label = null, $subLabel = null, $value = 1, $checked = null, array $options = []);

    /**
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function date($name, $label = null, $value = null, array $options = []);

    /**
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function time($name, $label = null, $value = null, array $options = []);

    /**
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function datetime($name, $label = null, $value = null, array $options = []);

    /**
     * @param string $name
     * @param string|null $label
     * @param string|null $value
     * @param array $options
     * @return string
     */
    public function editor($name, $label = null, $value = null, array $options = []);
}
