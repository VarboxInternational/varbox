<?php

namespace Varbox\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Varbox\Sorts\Sort;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Varbox\Exceptions\SortException;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait IsSortable
{
    /**
     * @var array
     */
    protected $sort = [
        /**
         * The query builder instance from the Sorted scope.
         *
         * @var Builder
         */
        'query' => null,

        /**
         * The data applying the "sorted" scope on a model.
         *
         * @var array
         */
        'data' => null,

        /**
         * The Neurony\Sort\Objects\Sort instance.
         * This is used to get the sorting rules, just like a request.
         *
         * @var Sort
         */
        'instance' => null,

        /**
         * The field to sort by.
         *
         * @var string
         */
        'field' => Sort::DEFAULT_SORT_FIELD,

        /**
         * The direction to sort in.
         *
         * @var string
         */
        'direction' => Sort::DEFAULT_DIRECTION_FIELD,
    ];

    /**
     * The filter scope.
     * Should be called on the model when building the query.
     *
     * @param Builder $query
     * @param array $data
     * @param Sort $sort
     */
    public function scopeSorted($query, array $data, Sort $sort = null)
    {
        $this->sort['query'] = $query;
        $this->sort['data'] = $data;
        $this->sort['instance'] = $sort;

        $this->setFieldToSortBy();
        $this->setDirectionToSortIn();

        if ($this->isValidSort()) {
            $this->checkSortingDirection();

            switch ($this->sort['data'][$this->sort['direction']]) {
                case Sort::DIRECTION_RANDOM:
                    $this->sort['query']->inRandomOrder();
                    break;
                default:
                    if ($this->shouldSortByRelation()) {
                        $this->sortByRelation();
                    } else {
                        $this->sortNormally();
                    }
            }
        }
    }

    /**
     * Verify if all sorting conditions are met.
     *
     * @return bool
     */
    protected function isValidSort()
    {
        return
            isset($this->sort['data'][$this->sort['field']]) &&
            isset($this->sort['data'][$this->sort['direction']]);
    }

    /**
     * Set the sort field if an Neurony\Sort\Objects\Sort instance has been provided as a parameter for the sorted scope.
     *
     * @return void
     */
    protected function setFieldToSortBy()
    {
        if ($this->sort['instance'] instanceof Sort) {
            $this->sort['field'] = $this->sort['instance']->field();
        }
    }

    /**
     * Set the sort direction if an Neurony\Sort\Objects\Sort instance has been provided as a parameter for the sorted scope.
     *
     * @return void
     */
    protected function setDirectionToSortIn()
    {
        if ($this->sort['instance'] instanceof Sort) {
            $this->sort['direction'] = $this->sort['instance']->direction();
        }
    }

    /**
     * Sort model records using columns from the model's table itself.
     *
     * @return void
     */
    protected function sortNormally()
    {
        $this->sort['query']->orderBy(
            $this->sort['data'][$this->sort['field']],
            $this->sort['data'][$this->sort['direction']]
        );
    }

    /**
     * Sort model records using columns from the model relation's table.
     *
     * @return void
     */
    protected function sortByRelation()
    {
        $parts = explode('.', $this->sort['data'][$this->sort['field']]);
        $models = [];

        if (count($parts) > 2) {
            $field = array_pop($parts);
            $relations = $parts;
        } else {
            $field = Arr::last($parts);
            $relations = (array) Arr::first($parts);
        }

        foreach ($relations as $index => $relation) {
            $previousModel = $this;

            if (isset($models[$index - 1])) {
                $previousModel = $models[$index - 1];
            }

            $this->checkRelationToSortBy($previousModel, $relation);

            $models[] = $previousModel->{$relation}()->getModel();

            $modelTable = $previousModel->getTable();
            $relationTable = $previousModel->{$relation}()->getModel()->getTable();
            $foreignKey = $previousModel->{$relation}()->getForeignKeyName();

            if (! $this->alreadyJoinedForSorting($relationTable)) {
                switch (get_class($previousModel->{$relation}())) {
                    case BelongsTo::class:
                        $this->sort['query']->join($relationTable, $modelTable.'.'.$foreignKey, '=', $relationTable.'.id');
                        break;
                    case HasOne::class:
                        $this->sort['query']->join($relationTable, $modelTable.'.id', '=', $relationTable.'.'.$foreignKey);
                        break;
                }
            }
        }

        $alias = implode('_', $relations).'_'.$field;

        if (isset($relationTable)) {
            $this->sort['query']->addSelect([
                $this->getTable().'.*',
                $relationTable.'.'.$field.' AS '.$alias,
            ]);
        }

        $this->sort['query']->orderBy(
            $alias, $this->sort['data'][$this->sort['direction']]
        );
    }

    /**
     * @return bool
     */
    protected function shouldSortByRelation()
    {
        return Str::contains($this->sort['data'][$this->sort['field']], '.');
    }

    /**
     * Verify if the desired join exists already, possibly included by a global scope.
     *
     * @param string $table
     *
     * @return bool
     */
    protected function alreadyJoinedForSorting($table)
    {
        return Str::contains(strtolower($this->sort['query']->toSql()), 'join `'.$table.'`');
    }

    /**
     * Verify if the direction provided matches one of the directions from:
     * Neurony\Sort\Objects\Sort::$directions.
     *
     * @return void
     */
    protected function checkSortingDirection()
    {
        if (! in_array(strtolower($this->sort['data'][$this->sort['direction']]), array_map('strtolower', Sort::$directions))) {
            throw SortException::invalidDirectionSupplied($this->sort['data'][$this->sort['direction']]);
        }
    }

    /**
     * Verify if the desired relation to sort by is one of: HasOne or BelongsTo.
     * Sorting by "many" relations or "morph" ones is not possible.
     *
     * @param Model $model
     * @param string $relation
     */
    protected function checkRelationToSortBy(Model $model, $relation)
    {
        if (! ($model->{$relation}() instanceof HasOne) && ! ($model->{$relation}() instanceof BelongsTo)) {
            throw SortException::wrongRelationToSort($relation, get_class($model->{$relation}()));
        }
    }
}
