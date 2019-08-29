<?php

namespace Varbox\Contracts;

interface LanguageModelContract
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeOnlyDefault($query);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeExcludingDefault($query);

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
}
