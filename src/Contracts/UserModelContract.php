<?php

namespace Varbox\Contracts;

interface UserModelContract
{
    /**
     * @return string
     */
    public function getFullNameAttribute();

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeAlphabetically($query);

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
    public function scopeOnlyAdmins($query);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeExcludingAdmins($query);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeOnlySuper($query);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     */
    public function scopeExcludingSuper($query);

    /**
     * @return bool
     */
    public function isActive();

    /**
     * @return bool
     */
    public function isInactive();

    /**
     * @return bool
     */
    public function isAdmin();

    /**
     * @return bool
     */
    public function isSuper();

    /**
     * @return mixed
     */
    public function getAuthIdentifier();

    /**
     * @return string
     */
    public function getAuthIdentifierName();

    /**
     * @return string
     */
    public function getRouteKeyName();

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token);
}
