<?php

namespace Varbox\Traits;

use Illuminate\Database\Query\Builder;
use Varbox\Database\QueryCacheBuilder;

trait IsCacheable
{
    /**
     * Boot the model.
     *
     * @return void
     */
    public static function bootIsCacheable()
    {
        static::saved(function ($model) {
            $model->clearQueryCache();
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
        return app('query_cache.service')->getAllQueryCachePrefix().'.'.(string) $this->getTable();
    }

    /**
     * @return string
     */
    public function getDuplicateQueryCacheTag(): string
    {
        return app('query_cache.service')->getDuplicateQueryCachePrefix().'.'.(string) $this->getTable();
    }

    /**
     * Flush the query cache from Redis only for the tag corresponding to the model instance.
     *
     * @return void
     */
    public function clearQueryCache(): void
    {
        app('query_cache.service')->clearQueryCache($this);
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

        if (app('query_cache.service')->canCacheQueries()) {
            if (app('query_cache.service')->shouldCacheAllQueries()) {
                $cacheAllQueriesForever = true;
            }

            if (app('query_cache.service')->shouldCacheDuplicateQueries()) {
                $cacheOnlyDuplicateQueriesOnce = true;
            }
        }

        if ($cacheAllQueriesForever === true) {
            return new QueryCacheBuilder(
                $connection, $grammar, $connection->getPostProcessor(),
                $this->getQueryCacheTag(), app('query_cache.service')->cacheAllQueriesForeverType()
            );
        }

        if ($cacheOnlyDuplicateQueriesOnce === true) {
            return new QueryCacheBuilder(
                $connection, $grammar, $connection->getPostProcessor(),
                $this->getDuplicateQueryCacheTag(), app('query_cache.service')->cacheOnlyDuplicateQueriesOnceType()
            );
        }

        return parent::newBaseQueryBuilder();
    }
}
