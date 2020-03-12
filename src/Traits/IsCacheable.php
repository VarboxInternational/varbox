<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Varbox\Database\Builders\QueryCacheBuilder;
use Varbox\Helpers\RelationHelper;

trait IsCacheable
{
    /**
     * Flag whether or not to cache queries.
     *
     * @var bool
     */
    protected static $canCacheQueries = true;

    /**
     * Boot the model.
     *
     * @return void
     */
    public static function bootIsCacheable()
    {
        static::saved(function ($model) {
            if ($model->isDirty()) {
                $model->clearQueryCache();
            }
        });

        static::deleted(function ($model) {
            $model->clearQueryCache();
        });
    }

    /**
     * @return string
     */
    public function getQueryCacheTag(): string
    {
        return config('varbox.query-cache.all.prefix') . '.' . (string) $this->getTable();
    }

    /**
     * @return string
     */
    public function getDuplicateQueryCacheTag(): string
    {
        return config('varbox.query-cache.duplicate.prefix') . '.' . (string) $this->getTable();
    }

    /**
     * Verify if forever query caching should run.
     *
     * @return bool
     */
    public function shouldCacheAllQueries(): bool
    {
        return config('varbox.query-cache.all.enabled', false) === true;
    }

    /**
     * Verify if caching of duplicate queries should run.
     *
     * @return bool
     */
    public function shouldCacheDuplicateQueries(): bool
    {
        return config('varbox.query-cache.duplicate.enabled', false) === true;
    }

    /**
     * Enable caching of database queries for the current request.
     * This is generally useful when working with rolled back database migrations.
     *
     * @return void
     */
    public function enableQueryCache(): void
    {
        static::$canCacheQueries = true;
    }

    /**
     * Disable caching of database queries for the current request.
     * This is generally useful when working with rolled back database migrations.
     *
     * @return void
     */
    public function disableQueryCache(): void
    {
        static::$canCacheQueries = false;
    }

    /**
     * Flush the query cache from the store only for the tag corresponding to the model instance.
     * If something fails, flush all existing cache for the specified store.
     * This way, it's guaranteed that nothing will be left out of sync at the database level.
     *
     * @return void
     * @throws Exception
     */
    public function clearQueryCache(): void
    {
        if (!(static::$canCacheQueries === true && ($this->shouldCacheAllQueries() || $this->shouldCacheDuplicateQueries()))) {
            return;
        }

        try {
            cache()->tags($this->getQueryCacheTag())->flush();

            foreach (RelationHelper::getModelRelations($this) as $relation => $attributes) {
                $related = $attributes['model'] ?? null;

                if (!($related instanceof Model && array_key_exists(IsCacheable::class, class_uses($related)))) {
                    continue;
                }

                cache()->tags($related->getQueryCacheTag())->flush();
            }
        } catch (Exception $e) {
            $this->flushQueryCache();
        }
    }

    /**
     * Flush all the query cache for the specified store.
     * Please note that this does not happen only for one caching type, but for all.
     *
     * @throws Exception
     */
    public function flushQueryCache(): void
    {
        if (static::$canCacheQueries !== true) {
            return;
        }

        if (static::$canCacheQueries === true && $this->shouldCacheAllQueries()) {
            cache()->flush();
        }
    }

    /**
     * Get a new query builder instance for the connection.
     *
     * @return Builder
     */
    protected function newBaseQueryBuilder(): Builder
    {
        $cacheAllQueriesForever = false;
        $cacheOnlyDuplicateQueriesOnce = false;

        $connection = $this->getConnection();
        $grammar = $connection->getQueryGrammar();

        if (static::$canCacheQueries) {
            if ($this->shouldCacheAllQueries()) {
                $cacheAllQueriesForever = true;
            } elseif ($this->shouldCacheDuplicateQueries()) {
                $cacheOnlyDuplicateQueriesOnce = true;
            }
        }

        if ($cacheAllQueriesForever === true) {
            return new QueryCacheBuilder(
                $connection, $grammar, $connection->getPostProcessor(),
                $this->getQueryCacheTag(), 'all-queries'
            );
        }

        if ($cacheOnlyDuplicateQueriesOnce === true) {
            return new QueryCacheBuilder(
                $connection, $grammar, $connection->getPostProcessor(),
                $this->getDuplicateQueryCacheTag(), 'duplicate-queries'
            );
        }

        return parent::newBaseQueryBuilder();
    }
}
