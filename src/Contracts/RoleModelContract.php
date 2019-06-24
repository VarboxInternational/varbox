<?php

namespace Varbox\Contracts;

interface RoleModelContract
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions();

    /**
     * @param string $name
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findByName($name);
}
