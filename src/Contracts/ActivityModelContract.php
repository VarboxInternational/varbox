<?php

namespace Varbox\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ActivityModelContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject();

    /**
     * @return string
     */
    public function getMessageAttribute();

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Varbox\Contracts\UserModelContract $user
     * @return mixed
     */
    public function scopeCausedBy($query, UserModelContract $user);

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Database\Eloquent\Model $subject
     * @return mixed
     */
    public function scopeForSubject($query, Model $subject);

    /**
     * @return array
     */
    public static function getDistinctEvents();

    /**
     * @return array
     */
    public static function getDistinctEntities();

    /**
     * @return void
     */
    public static function deleteOld();
}
