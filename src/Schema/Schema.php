<?php

namespace Varbox\Schema;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Varbox\Contracts\SchemaModelContract;
use Varbox\Helpers\RelationHelper;

abstract class Schema
{
    /**
     * The model instance for which to generate schema.
     *
     * @var Model
     */
    protected $model;

    /**
     * The schema model record.
     *
     * @var SchemaModelContract
     */
    protected $schema;

    /**
     * Generate the json+ld schema code.
     *
     * @return string
     */
    abstract public function generate();

    /**
     * @param Model $model
     * @param SchemaModelContract $schema
     */
    public function __construct(Model $model, SchemaModelContract $schema)
    {
        $this->model = $model;
        $this->schema = $schema;
    }

    /**
     * Normalize a field value.
     * The given field can be a normal/array/relation field or a hard-coded value.
     *
     * @param string $field
     * @return string
     */
    protected function normalizeValue($field)
    {
        $field = $this->schema->fields[$field] ?? null;

        if (!$field) {
            return null;
        }

        // is field value
        if (in_array($field, $this->schema->getTargetColumns())) {
            return $this->model->{$field} ?: null;
        }

        // is array field value
        if (Str::contains($field, '[') && Str::contains($field, ']')) {
            return $this->normalizeArrayValue($field);
        }

        // is relation value
        if (Str::contains($field, '.')) {
            $relations = RelationHelper::getModelRelations($this->model);
            $relation = Arr::first(explode('.', $field));

            if (array_key_exists($relation, $relations)) {
                return $this->normalizeRelationValue($field);
            }
        }

        // is hard-coded value
        return $field;
    }

    /**
     * Normalize a date field value.
     * The format the date is returned is ISO8601.
     *
     * @param string $field
     * @return string
     */
    protected function normalizeDateValue($field)
    {
        $value = $this->normalizeValue($field);

        if (!$value) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->toIso8601String();
        }

        try {
            return Carbon::parse($value)->toIso8601String();
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Normalize a url field value.
     * The url helper will is used in order to generate the valid url format.
     *
     * @param string $field
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    protected function normalizeUrlValue($field)
    {
        $value = $this->normalizeValue($field);

        if (!$value) {
            return null;
        }

        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return url($value);
        }

        return $value;
    }

    /**
     * Normalize an upload field value (image, video, audio, etc.)
     * The uploaded helper is used to generate the correct file url.
     *
     * @param string $field
     * @return string
     */
    protected function normalizeUploadValue($field)
    {
        $value = $this->normalizeValue($field);

        return $value ? uploaded($value)->url() : null;
    }

    /**
     * Normalize an array field value.
     * This is for json/array database columns.
     *
     * @param string $field
     * @return string
     */
    protected function normalizeArrayValue($field)
    {
        $column = strtok($field, '[');
        $value = Arr::get(
            get_object_vars_recursive($this->model->{$column}),
            str_replace('][', '.', trim(str_replace($column, '', $field), '.[]'))
        );

        return $value ? strip_tags($value) : null;
    }

    /**
     * Normalize a relation field value.
     * It only works with one-to-one or many-to-one relations.
     *
     * @param string $field
     * @return string
     */
    protected function normalizeRelationValue($field)
    {
        list($relation, $column) = explode('.', $field);

        return optional($this->model->{$relation})->{$column} ?: null;
    }
}
