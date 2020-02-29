<?php

namespace Varbox\Traits;

use Illuminate\Database\Eloquent\Model;
use Varbox\Options\OrderOptions;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use ReflectionMethod;

trait IsOrderable
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the App\Options\OrderOptions file.
     *
     * @var OrderOptions
     */
    protected static $orderOptions;

    /**
     * Boot the trait.
     *
     * @return void
     * @throws Exception
     */
    public static function bootIsOrderable()
    {
        self::checkOrderOptions();

        self::$orderOptions = self::getOrderOptions();

        static::creating(function ($model) {
            if ($model->shouldOrderWhenCreating()) {
                $model->setHighestOrderNumber();
            }
        });
    }

    /**
     * Sort the query by the order column ascending or descending.
     *
     * @param Builder $query
     * @param string $direction
     * @return Builder
     */
    public function scopeOrdered(Builder $query, $direction = 'asc')
    {
        return $query->orderBy($this->getOrderColumnName(), $direction);
    }

    /**
     * Decide if ordering when creating should happen.
     *
     * @return bool
     */
    public function shouldOrderWhenCreating()
    {
        return self::$orderOptions->orderWhenCreating === true;
    }

    /**
     * Modify the order column value to be the highest in the record set.
     *
     * @return void
     */
    public function setHighestOrderNumber()
    {
        $this->{$this->getOrderColumnName()} = $this->getHighestOrderNumber() + 1;
    }

    /**
     * Get the order value for the new record.
     *
     * @return int
     */
    public function getHighestOrderNumber()
    {
        return (int)$this->buildOrderQuery()->max($this->getOrderColumnName());
    }

    /**
     * Get the database table column on which the ordering is made.
     *
     * @return string
     */
    public function getOrderColumnName()
    {
        return self::$orderOptions->orderColumn;
    }

    /**
     * This function reorders the records:
     * The record with the first id in the array will get order 1.
     * The record with the second it will get order 2.
     * And so on until the last record.
     *
     * A starting order number can be optionally supplied (defaults to 1).
     *
     * @param array|\ArrayAccess $ids
     * @param int $start
     * @throws InvalidArgumentException
     */
    public static function setNewOrder($ids, $start = 0)
    {
        if (!is_array($ids)) {
            throw new InvalidArgumentException(
                'You must pass an array to setNewOrder'
            );
        }

        $model = new static;

        foreach ($ids as $id) {
            static::withoutGlobalScopes()->where($model->getKeyName(), $id)->update([
                $model->getOrderColumnName() => ++$start
            ]);
        }
    }

    /**
     * Swap the order of this model with the model 'above' this model.
     *
     * @return $this
     */
    public function moveOrderUp()
    {
        $column = $this->getOrderColumnName();
        $swap = $this->buildOrderQuery()->ordered('desc')->where($column, '<', $this->{$column})->first();

        if (!$swap) {
            return $this;
        }

        return $this->swapOrderWithModel($swap);
    }

    /**
     * Swap the order of this model with the model 'below' this model.
     *
     * @return $this
     */
    public function moveOrderDown()
    {
        $column = $this->getOrderColumnName();
        $swap = $this->buildOrderQuery()->ordered()->where($column, '>', $this->{$column})->first();

        if (!$swap) {
            return $this;
        }

        return $this->swapOrderWithModel($swap);
    }

    /**
     * Move this model to the first position.
     *
     * @return $this
     */
    public function moveToStart()
    {
        $first = $this->buildOrderQuery()->ordered()->first();

        if ($first->id === $this->id) {
            return $this;
        }

        $column = $this->getOrderColumnName();
        $this->{$column} = $first->{$column};

        $this->save();
        $this->buildOrderQuery()
            ->where($this->getKeyName(), '!=', $this->id)
            ->increment($column);

        return $this;
    }

    /**
     * Move this model to the last position.
     *
     * @return $this
     */
    public function moveToEnd()
    {
        $max = $this->getHighestOrderNumber();
        $column = $this->getOrderColumnName();

        if ($this->{$column} === $max) {
            return $this;
        }

        $old = $this->{$column};
        $this->{$column} = $max;

        $this->save();
        $this->buildOrderQuery()
            ->where($this->getKeyName(), '!=', $this->id)
            ->where($column, '>', $old)
            ->decrement($column);

        return $this;
    }

    /**
     * Swap the order of two models.
     *
     * @param Model $model
     * @param Model $other
     */
    public static function swapOrder(Model $model, Model $other)
    {
        $model->swapOrderWithModel($other);
    }

    /**
     * Swap the order of this model with the order of another model.
     *
     * @param Model $model
     * @return $this
     */
    public function swapOrderWithModel(Model $model)
    {
        $column = $this->getOrderColumnName();
        $old = $model->{$column};

        $model->{$column} = $this->{$column};
        $model->save();

        $this->{$column} = $old;
        $this->save();

        return $this;
    }

    /**
     * Build eloquent builder of orderable.
     *
     * @return Builder
     */
    public function buildOrderQuery()
    {
        return static::query();
    }

    /**
     * Verify if the getOrderOptions() method for setting the trait options exists and is public and static.
     *
     * @throws Exception
     */
    private static function checkOrderOptions()
    {
        if (!method_exists(self::class, 'getOrderOptions')) {
            throw new Exception(
                'The "' . self::class . '" must define the public static "getOrderOptions()" method.'
            );
        }

        $reflection = new ReflectionMethod(self::class, 'getOrderOptions');

        if (!$reflection->isPublic() || !$reflection->isStatic()) {
            throw new Exception(
                'The method "getOrderOptions()" from the class "' . self::class . '" must be declared as both "public" and "static".'
            );
        }
    }
}
