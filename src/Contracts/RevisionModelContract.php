<?php

namespace Varbox\Contracts;

interface RevisionModelContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function revisionable();

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Contracts\Auth\Authenticatable|int $user
     */
    public function scopeOfUser($query, $user);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     * @param string $type
     */
    public function scopeWhereRevisionable($query, $id, $type);
}
