<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface QueryCacheServiceContract
{
    /**
     * @return string
     */
    public function getAllQueryCacheStore(): string;

    /**
     * @return string
     */
    public function getDuplicateQueryCacheStore(): string;

    /**
     * @return string
     */
    public function getAllQueryCachePrefix(): string;

    /**
     * @return string
     */
    public function getDuplicateQueryCachePrefix(): string;

    /**
     * @return bool
     */
    public function shouldCacheAllQueries(): bool;

    /**
     * @return bool
     */
    public function shouldCacheDuplicateQueries(): bool;

    /**
     * @return void
     */
    public function enableQueryCache(): void;

    /**
     * @return void
     */
    public function disableQueryCache(): void;

    /**
     * @return bool
     */
    public function canCacheQueries(): bool;

    /**
     * @return void
     * @throws \Exception
     */
    public function flushQueryCache(): void;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function clearQueryCache(Model $model): void;
}
