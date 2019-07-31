<?php

namespace Varbox\Database\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class DraftingScope implements Scope
{
    /**
     * All of the extensions to be added to the builder.
     *
     * @var array
     */
    protected $extensions = [
        'WithDrafts', 'WithoutDrafts', 'OnlyDrafts'
    ];

    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->whereNull($model->getQualifiedDraftedAtColumn());
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param Builder $builder
     * @return void
     */
    public function extend(Builder $builder)
    {
        foreach ($this->extensions as $extension) {
            $this->{"add{$extension}"}($builder);
        }
    }

    /**
     * Add the with-drafts extension to the builder.
     *
     * @param Builder $builder
     * @return void
     */
    protected function addWithDrafts(Builder $builder)
    {
        $builder->macro('withDrafts', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }

    /**
     * Add the without-drafts extension to the builder.
     *
     * @param Builder $builder
     * @return void
     */
    protected function addWithoutDrafts(Builder $builder)
    {
        $builder->macro('withoutDrafts', function (Builder $builder) {
            $builder->withoutGlobalScope($this)->whereNull(
                $builder->getModel()->getQualifiedDraftedAtColumn()
            );

            return $builder;
        });
    }

    /**
     * Add the only-drafts extension to the builder.
     *
     * @param Builder $builder
     * @return void
     */
    protected function addOnlyDrafts(Builder $builder)
    {
        $builder->macro('onlyDrafts', function (Builder $builder) {
            $builder->withoutGlobalScope($this)->whereNotNull(
                $builder->getModel()->getQualifiedDraftedAtColumn()
            );

            return $builder;
        });
    }
}
