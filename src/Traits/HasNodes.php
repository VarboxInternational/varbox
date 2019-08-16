<?php

namespace Varbox\Traits;

use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;
use Kalnoy\Nestedset\QueryBuilder;

trait HasNodes
{
    use NodeTrait;

    /**
     * Get a new base query that includes deleted nodes.
     *
     * @param string|null $table
     * @return QueryBuilder
     */
    public function newNestedSetQuery($table = null)
    {
        $builder = $this->newQuery();

        if (array_key_exists(SoftDeletes::class, class_uses(static::class))) {
            $builder->withTrashed();
        }

        if (array_key_exists(IsDraftable::class, class_uses(static::class))) {
            $builder->withDrafts();
        }

        return $this->applyNestedSetScope($builder, $table);
    }

    /**
     * Call pending action.
     *
     * @return $this|void
     */
    /*protected function callPendingAction()
    {
        $this->moved = false;

        if (!$this->pending && ! $this->exists) {
            $this->dirtyBounds()->setNodeAction('root');
        }

        if (!$this->pending) {
            return;
        }

        list($method, $parameters) = [
            'action' . ucfirst(array_shift($this->pending)),
            $this->pending,
        ];

        $this->pending = null;
        $this->moved = call_user_func_array([
            $this, $method
        ], $parameters);

        $this->newNestedSetQuery()->fixTree();
    }*/

    /**
     * @param array|null $except
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function replicate(array $except = null)
    {
        $defaults = [
            $this->getLftName(),
            $this->getRgtName(),
        ];

        $except = $except ? array_unique(array_merge($except, $defaults)) : $defaults;

        return parent::replicate($except);
    }
}
