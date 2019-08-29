<?php

namespace Varbox\Contracts;

interface MenuModelContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menuable();

    /**
     * @return string|null
     */
    public function getUrlAttribute();

    /**
     * @return string|null
     */
    public function getUriAttribute();

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $id
     */
    public function scopeOfParent($query, $id);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeOnlyActive($query);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeOnlyInactive($query);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeAlphabetically($query);

    /**
     * @return array
     */
    public static function getRoutes();
}
