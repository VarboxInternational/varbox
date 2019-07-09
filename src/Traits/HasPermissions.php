<?php

namespace Varbox\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Varbox\Contracts\PermissionModelContract;
use Varbox\Contracts\RoleModelContract;
use Varbox\Models\Permission;

trait HasPermissions
{
    /**
     * A user belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        $permission = config('varbox.bindings.models.permission_model', Permission::class);

        return $this->belongsToMany($permission, 'user_permission')->withTimestamps();
    }

    /**
     * @param string|array|PermissionModelContract|Collection $permissions
     * @return HasPermissions
     */
    public function grantPermission($permissions)
    {
        try {
            if ($permissions instanceof PermissionModelContract) {
                $this->permissions()->save($permissions);
            } else {
                $this->permissions()->saveMany(
                    collect($permissions)->flatten()->map(function ($permission) {
                        return is_array($permission) || is_a($permission, Collection::class) ?
                            app('permission.model')->getPermissions($permission) : app('permission.model')->getPermission($permission);
                    })->all()
                );
            }

            $this->load('permissions');
        } catch (QueryException $e) {
            $this->revokePermission($permissions);
            $this->grantPermission($permissions);
        }

        return $this;
    }

    /**
     * @param string|array|PermissionModelContract|Collection $permissions
     * @return $this
     */
    public function revokePermission($permissions)
    {
        if ($permissions instanceof PermissionModelContract) {
            $this->permissions()->detach($permissions);
        } else {
            $this->permissions()->detach(
                (new Collection($permissions))->map(function ($permission) {
                    return is_array($permission) || is_a($permission, Collection::class) ?
                        app('permission.model')->getPermissions($permission) : app('permission.model')->getPermission($permission);
                })
            );
        }

        $this->load('permissions');

        return $this;
    }

    /**
     * @param string|array|PermissionModelContract|Collection $permissions
     * @return $this
     */
    public function syncPermissions($permissions)
    {
        $this->permissions()->detach();
        $this->grantPermission($permissions);

        return $this;
    }

    /**
     * @return array
     */
    public static function getAllGuards()
    {
        $guards = [];

        foreach (config('auth.guards') as $guard => $options) {
            $guards[$guard] = Str::title($guard);
        }

        return $guards;
    }

    /**
     * @return static
     */
    public static function getDefaultGuard()
    {
        return config('auth.defaults.guard');
    }
}
