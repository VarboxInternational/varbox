<?php

namespace Varbox\Services;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Varbox\Traits\IsCacheable;
use Varbox\Helpers\RelationHelper;
use Varbox\Contracts\QueryCacheServiceContract;

class QueryCacheService implements QueryCacheServiceContract
{
    /**
     * The model the cache should run on.
     * The model should use the IsCacheable trait for the whole process to work.
     *
     * @var Model
     */
    protected $model;

    /**
     * Flag whether or not to cache queries forever.
     *
     * @var bool
     */
    protected $cacheAllQueries = true;

    /**
     * Flag whether or not to cache only duplicate queries for the current request.
     *
     * @var bool
     */
    protected $cacheDuplicateQueries = true;

    /**
     * The query cache types available.
     *
     * @const
     */
    const TYPE_CACHE_ALL_QUERIES_FOREVER = 1;
    const TYPE_CACHE_ONLY_DUPLICATE_QUERIES_ONCE = 2;

    /**
     * Get the cache store to be used when caching queries forever.
     *
     * @return string
     */
    public function getAllQueryCacheStore(): string
    {
        return config('varbox.query-cache.query.all.store', 'array');
    }

    /**
     * Get the cache store to be used when caching only duplicate queries.
     *
     * @return string
     */
    public function getDuplicateQueryCacheStore(): string
    {
        return config('varbox.query-cache.query.duplicate.store', 'array');
    }

    /**
     * Get the cache prefix to be appended to the specific cache tag for the model instance.
     * Used when caching queries forever.
     *
     * @return string
     */
    public function getAllQueryCachePrefix(): string
    {
        return config('varbox.query-cache.query.all.prefix', 'cache.all_query');
    }

    /**
     * Get the cache prefix to be appended to the specific cache tag for the model instance.
     * Used when caching only duplicate queries.
     *
     * @return string
     */
    public function getDuplicateQueryCachePrefix(): string
    {
        return config('varbox.query-cache.query.duplicate.prefix', 'cache.duplicate_query');
    }

    /**
     * Verify if forever query caching should run.
     *
     * @return bool
     */
    public function shouldCacheAllQueries(): bool
    {
        return config('varbox.query-cache.query.all.enabled', false) === true;
    }

    /**
     * Verify if caching of duplicate queries should run.
     *
     * @return bool
     */
    public function shouldCacheDuplicateQueries(): bool
    {
        return config('varbox.query-cache.query.duplicate.enabled', false) === true;
    }

    /**
     * Get the "cache all queries forever" caching type.
     *
     * @return int
     */
    public function cacheAllQueriesForeverType(): int
    {
        return static::TYPE_CACHE_ALL_QUERIES_FOREVER;
    }

    /**
     * Get the "cache only duplicate queries once" caching type.
     *
     * @return int
     */
    public function cacheOnlyDuplicateQueriesOnceType(): int
    {
        return static::TYPE_CACHE_ONLY_DUPLICATE_QUERIES_ONCE;
    }

    /**
     * Enable caching of database queries for the current request.
     * This is generally useful when working with rolled back database migrations.
     *
     * @return void
     */
    public function enableQueryCache(): void
    {
        $this->cacheAllQueries = $this->cacheDuplicateQueries = true;
    }

    /**
     * Disable caching of database queries for the current request.
     * This is generally useful when working with rolled back database migrations.
     *
     * @return void
     */
    public function disableQueryCache(): void
    {
        $this->cacheAllQueries = $this->cacheDuplicateQueries = false;
    }

    /**
     * Verify if either forever query caching or duplicate query caching are enabled.
     *
     * @return bool
     */
    public function canCacheQueries(): bool
    {
        return $this->cacheAllQueries === true || $this->cacheDuplicateQueries === true;
    }

    /**
     * Flush all the query cache for the specified store.
     * Please note that this does not happen only for one caching type, but for all.
     *
     * @throws Exception
     */
    public function flushQueryCache(): void
    {
        if (! self::canCacheQueries()) {
            return;
        }

        if (self::shouldCacheAllQueries()) {
            cache()->store(self::getAllQueryCacheStore())->flush();
        }
    }

    /**
     * Flush the query cache from the store only for the tag corresponding to the model instance.
     * If something fails, flush all existing cache for the specified store.
     * This way, it's guaranteed that nothing will be left out of sync at the database level.
     *
     * @param Model $model
     * @return void
     * @throws Exception
     */
    public function clearQueryCache(Model $model): void
    {
        if (! ((self::shouldCacheAllQueries() || self::shouldCacheDuplicateQueries()) && self::canCacheQueries())) {
            return;
        }

        try {
            $this->model = $model;

            cache()->store(self::getAllQueryCacheStore())->tags($this->model->getQueryCacheTag())->flush();

            foreach (RelationHelper::getModelRelations($this->model) as $relation => $attributes) {
                if (
                    ($related = $attributes['model'] ?? null) && $related instanceof Model &&
                    array_key_exists(IsCacheable::class, class_uses($related))
                ) {
                    cache()->store(self::getAllQueryCacheStore())->tags($related->getQueryCacheTag())->flush();
                }
            }
        } catch (Exception $e) {
            self::flushQueryCache();
        }
    }
}
