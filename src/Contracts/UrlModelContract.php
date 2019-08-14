<?php

namespace Varbox\Contracts;

interface UrlModelContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function urlable();

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @param string $type
     */
    public function scopeWhereUrlable($query, $id, $type);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeAlphabetically($query);

    /**
     * @param bool $silent
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findUrlable($silent = true);

    /**
     * Get the model instance correlated with the accessed url.
     * Throw a ModelNotFoundException if the model doesn't exist.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findUrlableOrFail();
}
