<?php

namespace Varbox\Contracts;

interface PermissionModelContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles();

    /**
     * @param string $name
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByName($name);

    /**
     * @param string $guard
     * @return \Illuminate\Support\Collection
     */
    public static function getGrouped($guard);
}
