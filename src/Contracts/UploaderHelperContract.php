<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface UploaderHelperContract
{
    /**
     * @return \Illuminate\View\View
     * @throws \Varbox\Exceptions\UploadException
     */
    public function manager();

    /**
     * @param string|null $field
     * @return $this|string
     */
    public function field($field = null);

    /**
     * @param string|null $label
     * @return $this|string
     */
    public function label($label = null);

    /**
     * @param \Illuminate\Database\Eloquent\Model|null $model
     * @return $this|string
     */
    public function model(Model $model = null);

    /**
     * @param array|string $types
     * @return $this|array
     */
    public function types(...$types);

    /**
     * @param array|string $accept
     * @return $this|array
     */
    public function accept(...$accept);

    /**
     * @return $this
     */
    public function disabled();
}
