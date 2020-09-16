<?php

namespace Varbox\Traits;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Varbox\Contracts\RevisionModelContract;
use Varbox\Helpers\RelationHelper;
use Varbox\Models\Revision;
use Varbox\Options\RevisionOptions;

trait HasRevisions
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the Varbox\Options\RevisionOptions file.
     *
     * @var RevisionOptions
     */
    protected $revisionOptions;

    /**
     * Set the options for the HasRevisions trait.
     *
     * @return RevisionOptions
     */
    abstract public function getRevisionOptions(): RevisionOptions;

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootHasRevisions()
    {
        static::created(function (Model $model) {
            $model->createNewRevision();
        });

        static::updated(function (Model $model) {
            $model->createNewRevision();
        });

        static::deleted(function (Model $model) {
            if ($model->forceDeleting !== false) {
                $model->deleteAllRevisions();
            }
        });
    }

    /**
     * Register a revisioning model event with the dispatcher.
     *
     * @param Closure|string $callback
     * @return void
     */
    public static function revisioning($callback): void
    {
        static::registerModelEvent('revisioning', $callback);
    }

    /**
     * Register a revisioned model event with the dispatcher.
     *
     * @param Closure|string $callback
     * @return void
     */
    public static function revisioned($callback): void
    {
        static::registerModelEvent('revisioned', $callback);
    }

    /**
     * Get all the revisions for a given model instance.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function revisions()
    {
        $revision = config('varbox.bindings.models.revision_model', Revision::class);

        return $this->morphMany($revision, 'revisionable');
    }

    /**
     * Create a new revision record for the model instance.
     *
     * @return RevisionModelContract|bool|void
     */
    public function createNewRevision()
    {
        $this->initRevisionOptions();

        if ($this->wasRecentlyCreated && $this->revisionOptions->revisionOnCreate !== true) {
            return;
        }

        if (! $this->shouldCreateRevision()) {
            return false;
        }

        if ($this->fireModelEvent('revisioning') === false) {
            return false;
        }

        $revision = $this->saveAsRevision();

        $this->fireModelEvent('revisioned', false);

        return $revision;
    }

    /**
     * Manually save a new revision for a model instance.
     * This method should be called manually only where and if needed.
     *
     * @return RevisionModelContract
     */
    public function saveAsRevision()
    {
        $this->initRevisionOptions();

        return DB::transaction(function () {
            $revision = $this->revisions()->create([
                'user_id' => auth()->id() ?: null,
                'data' => $this->buildRevisionData(),
            ]);

            $this->clearOldRevisions();

            return $revision;
        });
    }

    /**
     * Rollback the model instance to the given revision instance.
     *
     * @param RevisionModelContract $revision
     * @return bool
     */
    public function rollbackToRevision(RevisionModelContract $revision)
    {
        $this->initRevisionOptions();

        static::revisioning(function () {
            return false;
        });

        DB::transaction(function () use ($revision) {
            if ($this->revisionOptions->createRevisionWhenRollingBack === true) {
                $this->saveAsRevision();
            }

            $this->rollbackModelToRevision($revision);

            if ($revision instanceof RevisionModelContract && isset($revision->data['relations'])) {
                foreach ($revision->data['relations'] as $relation => $attributes) {
                    if (RelationHelper::isDirect($attributes['type'])) {
                        $this->rollbackDirectRelationToRevision($relation, $attributes);
                    }

                    if (RelationHelper::isPivoted($attributes['type'])) {
                        $this->rollbackPivotedRelationToRevision($relation, $attributes);
                    }
                }
            }

            $revision->delete();
        });

        return true;
    }

    /**
     * Remove all existing revisions from the database, belonging to a model instance.
     *
     * @return void
     */
    public function deleteAllRevisions()
    {
        $this->revisions()->delete();
    }

    /**
     * If a revision record limit is set on the model and that limit is exceeded.
     * Remove the oldest revisions until the limit is met.
     *
     * @return void
     */
    public function clearOldRevisions()
    {
        $this->initRevisionOptions();

        $limit = $this->revisionOptions->revisionLimit;
        $count = $this->revisions()->count();

        if (is_numeric($limit) && $count > $limit) {
            $this->revisions()->oldest()->take($count - $limit)->delete();
        }
    }

    /**
     * Both instantiate the revision options as well as validate their contents.
     *
     * @return void
     */
    protected function initRevisionOptions(): void
    {
        if ($this->revisionOptions === null) {
            $this->revisionOptions = $this->getRevisionOptions();
        }
    }

    /**
     * Determine if a revision should be stored for a given model instance.
     *
     * Check the revisionable fields set on the model.
     * If any of those fields have changed, then a new revisions should be stored.
     * If no fields are specifically set on the model, this will return true.
     *
     * @return bool
     */
    protected function shouldCreateRevision()
    {
        $this->initRevisionOptions();

        $fieldsToRevision = $this->revisionOptions->revisionFields;
        $fieldsNotToRevision = $this->revisionOptions->revisionNotFields;

        if (
            array_key_exists(SoftDeletes::class, class_uses($this)) &&
            array_key_exists($this->getDeletedAtColumn(), $this->getDirty())
        ) {
            return false;
        }

        if ($fieldsToRevision && is_array($fieldsToRevision) && ! empty($fieldsToRevision)) {
            return $this->isDirty($fieldsToRevision);
        }

        if ($fieldsNotToRevision && is_array($fieldsNotToRevision) && ! empty($fieldsNotToRevision)) {
            return ! empty(Arr::except($this->getDirty(), $fieldsNotToRevision));
        }

        return true;
    }

    /**
     * Only rollback the model instance to the given revision.
     *
     * Loop through the revision's data.
     * If the revision's field name matches one from the model's attributes.
     * Replace the value from the model's attribute with the one from the revision.
     *
     * @param RevisionModelContract $revision
     * @return void
     */
    protected function rollbackModelToRevision(RevisionModelContract $revision)
    {
        foreach ($revision->data as $field => $value) {
            if (array_key_exists($field, $this->getAttributes())) {
                $this->attributes[$field] = $value;
            }
        }

        $this->save();
    }

    /**
     * Only rollback the model's direct relations to the given revision.
     *
     * Loop through the stored revision's relation items.
     * If the relation exists, then update it with the data from the revision.
     * If the relation does not exist, then create a new one with the data from the revision.
     *
     * Please note that when creating a new relation, the primary key (id) will be the old one from the revision's data.
     * This way, the correspondence between the model and it's relation is kept.
     *
     * @param string $relation
     * @param array $attributes
     * @return void
     */
    protected function rollbackDirectRelationToRevision($relation, $attributes)
    {
        $relatedPrimaryKey = $attributes['records']['primary_key'];
        $relatedRecords = $attributes['records']['items'];

        // delete extra added child related records after the revision checkpoint
        if (RelationHelper::isChild($attributes['type'])) {
            $oldRelated = $this->{$relation}()->pluck($relatedPrimaryKey)->toArray();

            $currentRelated = array_map(function ($item) use ($relatedPrimaryKey) {
                return $item[$relatedPrimaryKey];
            }, $relatedRecords);

            $extraRelated = array_diff($oldRelated, $currentRelated);

            if (! empty($extraRelated)) {
                $this->{$relation}()->whereIn($relatedPrimaryKey, $extraRelated)->delete();
            }
        }

        // rollback each related record to its revision checkpoint
        foreach ($relatedRecords as $item) {
            $related = $this->{$relation}();

            if (array_key_exists(SoftDeletes::class, class_uses($this->{$relation}))) {
                $related = $related->withTrashed();
            }

            $rel = $related->findOrNew($item[$relatedPrimaryKey] ?? null);

            foreach ($item as $field => $value) {
                $rel->attributes[$field] = $value;
            }

            if (array_key_exists(SoftDeletes::class, class_uses($rel))) {
                $rel->{$rel->getDeletedAtColumn()} = null;
            }

            $rel->save();
        }
    }

    /**
     * Rollback a model's pivoted relations to the given revision.
     *
     * Loop through the stored revision's relation items.
     * If the relation's related model exists, then leave it as is (maybe modified) because other records or entities might be using it.
     * If the relation's related model does not exist, then create a new one with the data from the revision.
     *
     * Please note that when creating a new relation related instance, the primary key (id) will be the old one from the revision's data.
     * This way, the correspondence between the model and it's relation is kept.
     *
     * Loop through the stored revision's relation pivots.
     * Sync the model's pivot values with the ones from the revision.
     *
     * @param string $relation
     * @param array $attributes
     * @return void
     */
    protected function rollbackPivotedRelationToRevision($relation, $attributes)
    {
        foreach ($attributes['records']['items'] as $item) {
            $related = $this->{$relation}()->getRelated();

            if (array_key_exists(SoftDeletes::class, class_uses($related))) {
                $related = $related->withTrashed();
            }

            $rel = $related->findOrNew($item[$attributes['records']['primary_key']] ?? null);

            if ($rel->exists === false) {
                foreach ($item as $field => $value) {
                    $rel->attributes[$field] = $value;
                }

                if (array_key_exists(SoftDeletes::class, class_uses($rel))) {
                    $rel->{$rel->getDeletedAtColumn()} = null;
                }

                $rel->save();
            }
        }

        $this->{$relation}()->detach();

        foreach ($attributes['pivots']['items'] as $item) {
            $this->{$relation}()->attach(
                $item[$attributes['pivots']['related_key']],
                Arr::except((array) $item, [
                    $attributes['pivots']['primary_key'],
                    $attributes['pivots']['foreign_key'],
                    $attributes['pivots']['related_key'],
                ])
            );
        }
    }

    /**
     * Build the entire data array for further json insert into the revisions table.
     *
     * Extract the actual model's data.
     * Extract all of the model's direct relations data.
     * Extract all of the model's pivoted relations data.
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function buildRevisionData()
    {
        $data = $this->buildRevisionDataFromModel();

        foreach ($this->getRelationsForRevision() as $relation => $attributes) {
            if (RelationHelper::isDirect($attributes['type'])) {
                $data['relations'][$relation] = $this->buildRevisionDataFromDirectRelation($relation, $attributes);
            }

            if (RelationHelper::isPivoted($attributes['type'])) {
                $data['relations'][$relation] = $this->buildRevisionDataFromPivotedRelation($relation, $attributes);
            }
        }

        return $data;
    }

    /**
     * Get all the fields that should be revisioned from the model instance.
     * Automatically unset primary and timestamp keys.
     * Also count for revision fields if any are set on the model.
     *
     * @return array
     */
    protected function buildRevisionDataFromModel()
    {
        $data = $this->wasRecentlyCreated === true ? $this->getAttributes() : $this->getRawOriginal();

        $fieldsToRevision = $this->revisionOptions->revisionFields;
        $fieldsNotToRevision = $this->revisionOptions->revisionNotFields;

        unset($data[$this->getKeyName()]);

        if ($this->usesTimestamps() && ! $this->revisionOptions->revisionTimestamps) {
            unset($data[$this->getCreatedAtColumn()]);
            unset($data[$this->getUpdatedAtColumn()]);
        }

        if ($fieldsToRevision && is_array($fieldsToRevision) && ! empty($fieldsToRevision)) {
            foreach ($data as $field => $value) {
                if (! in_array($field, $fieldsToRevision)) {
                    unset($data[$field]);
                }
            }
        } elseif ($fieldsNotToRevision && is_array($fieldsNotToRevision) && ! empty($fieldsNotToRevision)) {
            foreach ($data as $field => $value) {
                if (in_array($field, $fieldsNotToRevision)) {
                    unset($data[$field]);
                }
            }
        }

        return $data;
    }

    /**
     * Extract revisionable data from a model's relation.
     * Extract the type, class and related records.
     * Store the extracted data into an array to be json inserted into the revisions table.
     *
     * @param string $relation
     * @param array $attributes
     * @return array
     */
    protected function buildRevisionDataFromDirectRelation($relation, $attributes = [])
    {
        $data = [
            'type' => $attributes['type'],
            'class' => get_class($attributes['model']),
            'records' => [
                'primary_key' => null,
                'foreign_key' => null,
                'items' => [],
            ],
        ];

        foreach ($this->{$relation}()->get() as $index => $model) {
            $data = $this->dataWithForeignKeys(
                $data, $model->getKeyName(), $this->getForeignKey()
            );

            foreach ($model->getRawOriginal() as $field => $value) {
                $data = $this->dataWithAttributeValue(
                    $data, $model->getAttributes(), $index, $field, $value
                );
            }
        }

        return $data;
    }

    /**
     * Extract revisionable data from a model's relation pivot table.
     * Extract the type, class, related records and pivot values.
     * Store the extracted data into an array to be json inserted into the revisions table.
     *
     * @param string $relation
     * @param array $attributes
     * @return array
     */
    protected function buildRevisionDataFromPivotedRelation($relation, $attributes = [])
    {
        $data = [
            'type' => $attributes['type'],
            'class' => get_class($attributes['model']),
            'records' => [
                'primary_key' => null,
                'foreign_key' => null,
                'items' => [],
            ],
            'pivots' => [
                'primary_key' => null,
                'foreign_key' => null,
                'related_key' => null,
                'items' => [],
            ],
        ];

        foreach ($this->{$relation}()->get() as $index => $model) {
            $accessor = $this->{$relation}()->getPivotAccessor();
            $pivot = $model->{$accessor};

            foreach ($model->getRawOriginal() as $field => $value) {
                $data = $this->dataWithForeignKeys(
                    $data, $model->getKeyName(), $this->getForeignKey()
                );

                $data = $this->dataWithAttributeValue(
                    $data, $model->getAttributes(), $index, $field, $value
                );
            }

            foreach ($pivot->getRawOriginal() as $field => $value) {
                $data = $this->dataWithPivotForeignKeys(
                    $data, $pivot->getKeyName(), $pivot->getForeignKey(), $pivot->getRelatedKey()
                );

                $data = $this->dataWithPivotAttributeValue(
                    $data, $pivot->getAttributes(), $index, $field, $value
                );
            }
        }

        return $data;
    }

    /**
     * Verify if the data array contains the foreign keys.
     *
     * @param array $data
     * @return bool
     */
    protected function dataHasForeignKeys($data = [])
    {
        return $data['records']['primary_key'] && $data['records']['foreign_key'];
    }

    /**
     * Verify if the data array contains the pivoted foreign keys.
     *
     * @param array $data
     * @return bool
     */
    protected function dataHasPivotForeignKeys($data = [])
    {
        return $data['pivots']['primary_key'] && $data['pivots']['foreign_key'] && $data['pivots']['related_key'];
    }

    /**
     * Attach the foreign keys to the data array.
     *
     * @param array $data
     * @param string $primaryKey
     * @param string $foreignKey
     * @return array
     */
    protected function dataWithForeignKeys($data, $primaryKey, $foreignKey)
    {
        if (! $this->dataHasForeignKeys($data)) {
            $data['records']['primary_key'] = $primaryKey;
            $data['records']['foreign_key'] = $foreignKey;
        }

        return $data;
    }

    /**
     * Attach the pivoted foreign keys to the data array.
     *
     * @param array $data
     * @param string $primaryKey
     * @param string $foreignKey
     * @param string $relatedKey
     * @return array
     */
    protected function dataWithPivotForeignKeys($data, $primaryKey, $foreignKey, $relatedKey)
    {
        if (! $this->dataHasPivotForeignKeys($data)) {
            $data['pivots']['primary_key'] = $primaryKey;
            $data['pivots']['foreign_key'] = $foreignKey;
            $data['pivots']['related_key'] = $relatedKey;
        }

        return $data;
    }

    /**
     * Build the data array with each attribute<->value set for the given model.
     *
     * @param array $data
     * @param array $attributes
     * @param int $index
     * @param string $field
     * @param string|int|null $value
     * @return array
     */
    protected function dataWithAttributeValue($data, $attributes, $index, $field, $value = null)
    {
        if (array_key_exists($field, $attributes)) {
            $data['records']['items'][$index][$field] = $value;
        }

        return $data;
    }

    /**
     * Build the data array with each pivoted attribute<->value set for the given model.
     *
     * @param array $data
     * @param array $attributes
     * @param int $index
     * @param string $field
     * @param string|int|null $value
     * @return array
     */
    protected function dataWithPivotAttributeValue($data, $attributes, $index, $field, $value = null)
    {
        if (array_key_exists($field, $attributes)) {
            $data['pivots']['items'][$index][$field] = $value;
        }

        return $data;
    }

    /**
     * Get the relations that should be revisionable alongside the original model.
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function getRelationsForRevision()
    {
        $relations = [];

        foreach (RelationHelper::getModelRelations($this) as $relation => $attributes) {
            if (in_array($relation, $this->revisionOptions->revisionRelations)) {
                $relations[$relation] = $attributes;
            }
        }

        return $relations;
    }
}
