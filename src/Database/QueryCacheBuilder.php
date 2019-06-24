<?php

namespace Varbox\Database;

use Exception;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Query\Builder as QueryBuilder;

class QueryCacheBuilder extends QueryBuilder
{
    /**
     * The cache tag value.
     * The value comes from the Neurony\QueryCache\Traits\IsCacheable.
     *
     * @var string
     */
    protected $cacheTag;

    /**
     * The cache type value.
     * Can have one of the values present in the QueryCache class -> TYPE_CACHE constants.
     * The value comes from the Neurony\QueryCache\IsCacheable.
     *
     * @var string
     */
    protected $cacheType;

    /**
     * Create a new query builder instance.
     *
     * @param ConnectionInterface $connection
     * @param Grammar|null $grammar
     * @param Processor|null $processor
     * @param string|null $cacheTag
     * @param int|null $cacheType
     */
    public function __construct(
        ConnectionInterface $connection,
        Grammar $grammar = null,
        Processor $processor = null,
        $cacheTag = null, $cacheType = null
    ) {
        parent::__construct($connection, $grammar, $processor);

        $this->cacheType = $cacheType;
        $this->cacheTag = $cacheTag;
    }

    /**
     * Returns a unique string that can identify this query.
     *
     * @return string
     */
    public function getQueryCacheKey(): string
    {
        return json_encode([
            $this->toSql() => $this->getBindings(),
        ]);
    }

    /**
     * Flush the query cache based on the model's cache tag.
     *
     * @return void
     * @throws Exception
     */
    public function flushQueryCache(): void
    {
        cache()->store(
            app('query_cache.service')->getAllQueryCacheStore()
        )->tags($this->cacheTag)->flush();
    }

    /**
     * Insert a new record into the database.
     *
     * @param array $values
     * @return bool
     * @throws Exception
     */
    public function insert(array $values): bool
    {
        $this->flushQueryCache();

        return parent::insert($values);
    }

    /**
     * Update a record in the database.
     *
     * @param array $values
     * @return int
     * @throws Exception
     */
    public function update(array $values): int
    {
        $this->flushQueryCache();

        return parent::update($values);
    }

    /**
     * Delete a record from the database.
     *
     * @param int|null $id
     * @return int|null
     * @throws Exception
     */
    public function delete($id = null): ?int
    {
        $this->flushQueryCache();

        return parent::delete($id);
    }

    /**
     * Run a truncate statement on the table.
     *
     * @return void
     * @throws Exception
     */
    public function truncate(): void
    {
        $this->flushQueryCache();

        parent::truncate();
    }

    /**
     * Run the query as a "select" statement against the connection.
     *
     * @return array
     * @throws Exception
     */
    protected function runSelect(): array
    {
        switch ($this->cacheType) {
            case app('query_cache.service')->cacheAllQueriesForeverType():
                return $this->runSelectWithAllQueriesCached();
                break;
            case app('query_cache.service')->cacheOnlyDuplicateQueriesOnceType():
                return $this->runSelectWithDuplicateQueriesCached();
                break;
            default:
                return parent::runSelect();
                break;
        }
    }

    /**
     * Run the query as a "select" statement against the connection.
     * Also while fetching the results, cache all queries.
     *
     * @return mixed
     * @throws Exception
     */
    protected function runSelectWithAllQueriesCached()
    {
        return cache()->store(
            app('query_cache.service')->getAllQueryCacheStore()
        )->tags($this->cacheTag)->rememberForever($this->getQueryCacheKey(), function () {
            return parent::runSelect();
        });
    }

    /**
     * Run the query as a "select" statement against the connection.
     * Also while fetching the results, cache only duplicate queries for the current request.
     *
     * @return mixed
     * @throws Exception
     */
    protected function runSelectWithDuplicateQueriesCached()
    {
        return cache()->store(
            app('query_cache.service')->getDuplicateQueryCacheStore()
        )->tags($this->cacheTag)->remember($this->getQueryCacheKey(), 1, function () {
            return parent::runSelect();
        });
    }
}
