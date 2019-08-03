<?php

namespace Varbox\Traits;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Varbox\Database\Scopes\DraftingScope;

trait IsDraftable
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootIsDraftable()
    {
        static::addGlobalScope(new DraftingScope);
    }

    /**
     * Register a drafting model event with the dispatcher.
     *
     * @param Closure|string  $callback
     * @return void
     */
    public static function drafting($callback)
    {
        static::registerModelEvent('drafting', $callback);
    }

    /**
     * Register a drafted model event with the dispatcher.
     *
     * @param Closure|string  $callback
     * @return void
     */
    public static function drafted($callback)
    {
        static::registerModelEvent('drafted', $callback);
    }

    /**
     * @param array $data
     * @return Model|bool
     * @throws Exception
     */
    public function saveAsDraft(array $data = [])
    {
        try {
            if ($this->fireModelEvent('drafting') === false) {
                return false;
            }

            $draft = DB::transaction(function () use ($data) {
                $model = $this->updateOrCreateDraft($data);

                $this->newQueryWithoutScopes()->where($model->getKeyName(), $model->getKey())->update([
                    $this->getDraftedAtColumn() => $this->fromDateTime($this->freshTimestamp())
                ]);

                return $model;
            });

            $draft->fireModelEvent('drafted', true);

            return $draft->fresh();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Publish a draft.
     * Make the "drafted_at" column equal to null.
     *
     * @return Model
     */
    public function publishDraft()
    {
        $this->{$this->getDraftedAtColumn()} = null;
        $this->save();

        return $this->fresh();
    }

    /**
     * Determine if a model instance is a draft.
     *
     * @return bool
     */
    public function isDrafted()
    {
        return $this->{$this->getDraftedAtColumn()} !== null;
    }

    /**
     * Get the name of the draft column.
     *
     * @return string
     */
    public function getDraftedAtColumn()
    {
        return 'drafted_at';
    }

    /**
     * Get the fully qualified draft column.
     *
     * @return string
     */
    public function getQualifiedDraftedAtColumn()
    {
        return $this->getTable() . '.' . $this->getDraftedAtColumn();
    }

    /**
     * Update or create a model instance before drafting it.
     *
     * @param array $data
     * @return $this
     */
    protected function updateOrCreateDraft(array $data = [])
    {
        if ($this->exists) {
            $this->update($data);

            return $this;
        }

        return $this->create($data);
    }
}
