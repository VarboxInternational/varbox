<?php

namespace Varbox\Traits;

use BadMethodCallException;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Varbox\Exceptions\FilterException;
use Varbox\Filters\Filter;

trait IsFilterable
{
    protected $filter = [
        /**
         * The query builder instance from the "filtered" scope.
         *
         * @var Builder
         */
        'query' => null,

        /**
         * The Varbox\Filters\Filter instance.
         * This is used to get the filtering rules, just like a request.
         *
         * @var Filter
         */
        'instance' => null,

        /**
         * The data passed from applying the "filtered" scope on a model.
         *
         * @var array
         */
        'data' => [],

        /**
         * The main where condition between entire fields.
         * This can be either "and" | "or".
         *
         * @var
         */
        'morph' => null,

        /**
         * The actual where method used to filter: {or}Where{Not}{Null}{In}{Between}{Date}.
         *
         * @var
         */
        'method' => null,

        /**
         * The actual whereHas method used to filter by relations: {or}WhereHas.
         *
         * @var
         */
        'having' => null,

        /**
         * The field name from a GET request.
         *
         * @var
         */
        'field' => null,

        /**
         * The actual value of the GET request field.
         *
         * @var
         */
        'value' => null,

        /**
         * Used for correctly filtering the records.
         * One of the values from Varbox\Filters\Filter::$operators.
         *
         * @var
         */
        'operator' => null,

        /**
         * Used for conditioning the filter on multiple columns.
         * One of the values from Varbox\Filters\Filter::$conditions.
         *
         * @var
         */
        'condition' => null,

        /**
         * One or more column names from the model's table.
         *
         * @var
         */
        'columns' => null,
    ];

    /**
     * The filter scope.
     * Should be called when fetching the results.
     *
     * @param Builder $query
     * @param array $data
     * @param Filter $filter
     */
    public function scopeFiltered($query, array $data, Filter $filter)
    {
        $this->filter['query'] = $query;
        $this->filter['data'] = $data;
        $this->filter['instance'] = $filter;

        foreach ($this->filter['instance']->filters() as $field => $options) {
            $this->filter['field'] = $field;

            if ($this->isValidFilter()) {
                $this->setOperatorForFiltering($options);
                $this->setConditionToFilterBy($options);
                $this->setColumnsToFilterIn($options);
                $this->setMethodsOfFiltering();
                $this->setValueToFilterBy();

                $this->checkOperatorForFiltering();
                $this->checkConditionToFilterBy();
                $this->checkColumnsToFilterIn();

                $this->morph()->filter();
            }
        }
    }

    /**
     * Get the morph type defined in the Varbox\Filters\Filter corresponding class.
     * Build the general where method based on the morph.
     * Morph can only be "and" or "or".
     *
     * @return $this
     */
    protected function morph()
    {
        $this->filter['morph'] = 'where';

        if (strtolower($this->filter['instance']->morph()) == 'or') {
            $this->filter['morph'] = 'or' . ucwords($this->filter['morph']);
        }

        return $this;
    }

    /**
     * Filter the records.
     * The filtering takes into consideration fluid/descriptive where methods.
     * orWhere, whereNot, whereNull, whereIn, whereBetween, whereDate, etc.
     *
     * @return void
     */
    protected function filter()
    {
        $this->filter['query']->{$this->filter['morph']}(function ($query) {
            foreach (explode(',', trim($this->filter['columns'], ',')) as $column) {
                if ($this->shouldFilterByRelation($column)) {
                    $this->filterByRelation($query, $column);
                } else {
                    $this->filterNormally($query, $column);
                }
            }
        });
    }

    /**
     * Filter model records based on a relation defined.
     * Relation can be hasOne, hasMany, belongsTo or hasAndBelongsToMany.
     *
     * @param Builder $query
     * @param string $column
     * @return void
     */
    protected function filterByRelation(Builder $query, $column)
    {
        $options = [];
        $relation = Str::camel(explode('.', $column)[0]);
        $options[$relation][] = explode('.', $column)[1];

        foreach ($options as $relation => $columns) {
            try {
                $query->{$this->filter['having']}($relation, function ($q) use ($columns) {
                    foreach ($columns as $index => $column) {
                        $method = $index == 0 ? lcfirst(str_replace('or', '', $this->filter['method'])) : $this->filter['method'];

                        $this->filterIndividually($q, $method, $column);
                    }
                });
            } catch (BadMethodCallException $e) {
                $this->filterIndividually($query, $this->filter['method'], $column);
            }
        }
    }

    /**
     * Filter model records using columns from the model's table itself.
     *
     * @param Builder $query
     * @param string $column
     * @return void
     */
    protected function filterNormally(Builder $query, $column)
    {
        $this->filterIndividually($query, $this->filter['method'], $column);
    }

    /**
     * Abstraction of filtering to use in filtering by relations or normally.
     *
     * @param Builder $query
     * @param string $method
     * @param string $column
     * @return void
     */
    protected function filterIndividually(Builder $query, $method, $column)
    {
        switch ($_method = strtolower($method)) {
            case Str::contains($_method, Filter::OPERATOR_NULL):
                $query->{$method}($column);
                break;
            case Str::contains($_method, Filter::OPERATOR_IN):
                $query->{$method}($column, $this->filter['value']);
                break;
            case Str::contains($_method, Filter::OPERATOR_BETWEEN):
                $query->{$method}($column, $this->filter['value']);
                break;
            case Str::contains($_method, Filter::OPERATOR_DATE):
                $operator = explode(' ', $this->filter['operator']);
                $query->{$method}($column, ($operator[1] ?? '='), $this->filter['value']);
                break;
            default:
                $query->{$method}($column, $this->filter['operator'], $this->filter['value']);
                break;
        }
    }

    /**
     * Verify if all filtering conditions are met.
     *
     * @return bool
     */
    protected function isValidFilter()
    {
        return $this->isValidFilterField() && !$this->isNullFilterField();
    }

    /**
     * Verify if field has a valid value.
     *
     * @return bool
     */
    protected function isValidFilterField()
    {
        return
            isset($this->filter['data'][$this->filter['field']]) ||
            in_array($this->filter['field'], Filter::$fields);
    }

    /**
     * Verify if the entire data array consists only of null values or not.
     *
     * @return bool
     */
    protected function isNullFilterField()
    {
        if (is_array($this->filter['data'][$this->filter['field']])) {
            $count = 0;

            foreach ($this->filter['data'][$this->filter['field']] as $value) {
                if ($value === null) {
                    $count++;
                }
            }

            return $count == count($this->filter['data'][$this->filter['field']]);
        }

        return is_null($this->filter['data'][$this->filter['field']]);
    }

    /**
     * Determine if filtering should focus on a subsequent relationship.
     * The convention here is to use dot ".", separating the table from column.
     *
     * @param string $column
     * @return bool
     */
    protected function shouldFilterByRelation($column)
    {
        return Str::contains($column, '.');
    }

    /**
     * Set the proper filtering method.
     * Also takes into consideration fluid/descriptive where methods.
     *
     * @return void
     */
    protected function setMethodsOfFiltering()
    {
        $this->filter['method'] = 'where';
        $this->filter['having'] = 'whereHas';

        if ($this->filter['condition'] == Filter::CONDITION_OR) {
            $this->filter['method'] = 'or' . ucwords($this->filter['method']);
            $this->filter['having'] = 'or' . ucwords($this->filter['having']);
        }

        switch ($operator = strtolower($this->filter['operator'])) {
            case Str::contains($operator, 'null'):
                $this->attemptToBuildNotMethod();

                $this->filter['method'] = $this->filter['method'] . 'Null';
                break;
            case Str::contains($operator, 'in'):
                $this->attemptToBuildNotMethod();

                $this->filter['method'] = $this->filter['method'] . 'In';
                break;
            case Str::contains($operator, 'between'):
                $this->attemptToBuildNotMethod();

                $this->filter['method'] = $this->filter['method'] . 'Between';
                break;
            case Str::contains($operator, 'date'):
                $this->attemptToBuildNotMethod();

                $this->filter['method'] = $this->filter['method'] . 'Date';
                break;
        }
    }

    /**
     * @return void
     */
    protected function attemptToBuildNotMethod()
    {
        if (Str::contains(strtolower($this->filter['operator']), 'not')) {
            $this->filter['method'] = $this->filter['method'] . 'Not';
        }
    }

    /**
     * Set the value accordingly to the operator used.
     * Some of the operators require the value to be processed.
     * Also, this method handles the modifiers() method if defined on the filter class.
     *
     * @return void
     */
    protected function setValueToFilterBy()
    {
        if (
            method_exists($this->filter['instance'], 'modifiers') &&
            array_key_exists($this->filter['field'], $this->filter['instance']->modifiers())
        ) {
            foreach ($this->filter['instance']->modifiers() as $field => $value) {
                if ($field == $this->filter['field']) {
                    $this->filter['value'] = $value instanceof Closure ? $value(null) : $value;
                    break;
                }
            }
        } else {
            $this->filter['value'] = $this->filter['data'][$this->filter['field']];
        }

        switch ($operator = strtolower($this->filter['operator'])) {
            case Str::contains($operator, Filter::OPERATOR_LIKE):
                $this->filter['value'] = "%" . $this->filter['value'] . "%";
                break;
            case Str::contains($operator, Filter::OPERATOR_IN):
                $this->filter['value'] = (array)$this->filter['value'];
                break;
            case Str::contains($operator, Filter::OPERATOR_BETWEEN):
                if (!isset($this->filter['value'][0])) {
                    $this->filter['value'][0] = 0;
                }

                if (!isset($this->filter['value'][1])) {
                    $this->filter['value'][1] = 0;
                }

                if (
                    isset($this->filter['value'][0]) && $this->filter['value'][0] > 0 &&
                    isset($this->filter['value'][1]) && $this->filter['value'][1] == 0
                ) {
                    $this->filter['value'][1] = 999 * 999 * 999 * 999 * 999;
                }

                $this->filter['value'] = (array)$this->filter['value'];
                break;
        }
    }

    /**
     * Set the operator for filtering.
     * This is done based on the string defined in Varbox\Filters\Filter corresponding class.
     *
     * @param array $options
     */
    protected function setOperatorForFiltering(array $options)
    {
        $this->filter['operator'] = $options['operator'] ?? null;
    }

    /**
     * Set the condition to filter by.
     * This is done based on the string defined in Varbox\Filters\Filter corresponding class.
     *
     * @param array $options
     */
    protected function setConditionToFilterBy(array $options)
    {
        $this->filter['condition'] = $options['condition'] ?? null;
    }

    /**
     * Set the columns to filter in.
     * This is done based on the string defined in Varbox\Filters\Filter corresponding class.
     *
     * @param array $options
     */
    protected function setColumnsToFilterIn(array $options)
    {
        $this->filter['columns'] = $options['columns'] ?? null;
    }

    /**
     * Verify if the operator has been properly set.
     * If not, throw a descriptive error for the developer to amend.
     *
     * @return void
     */
    protected function checkOperatorForFiltering()
    {
        if (
            !isset($this->filter['operator']) || 
            !in_array(strtolower($this->filter['operator']), array_map('strtolower', Filter::$operators))
        ) {
            throw FilterException::noOperatorSupplied($this->filter['field'], get_class($this->filter['instance']));
        }
    }

    /**
     * Verify if the condition has been properly set.
     * If not, throw a descriptive error for the developer to amend.
     *
     * @return void
     */
    protected function checkConditionToFilterBy()
    {
        if (
            !isset($this->filter['condition']) || 
            !in_array(strtolower($this->filter['condition']), array_map('strtolower', Filter::$conditions))
        ) {
            throw FilterException::noConditionSupplied($this->filter['field'], get_class($this->filter['instance']));
        }
    }

    /**
     * Verify if the columns have been properly set.
     * If not, throw a descriptive error for the developer to amend.
     *
     * @return void
     */
    protected function checkColumnsToFilterIn()
    {
        if (!isset($this->filter['columns']) || empty($this->filter['columns'])) {
            throw FilterException::noColumnsSupplied($this->filter['field'], get_class($this->filter['instance']));
        }
    }
}
