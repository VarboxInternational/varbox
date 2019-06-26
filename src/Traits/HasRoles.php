<?php

namespace Varbox\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as SupportCollection;
use Varbox\Contracts\PermissionModelContract;
use Varbox\Contracts\RoleModelContract;
use Varbox\Models\Role;

trait HasRoles
{
    use HasPermissions;

    /**
     * A user belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        $role = config('varbox.varbox-binding.models.role_model', Role::class);

        return $this->belongsToMany($role, 'user_role')->withTimestamps();
    }

    /**
     * Filter the query by the given roles.
     *
     * @param $query
     * @param string|array|RoleModelContract|Collection $roles
     * @return mixed
     */
    public function scopeWithRoles($query, $roles)
    {
        if ($roles instanceof RoleModelContract) {
            $roles = [$roles];
        } else {
            $roles = collect($roles)->map(function ($role) {
                if ($role instanceof RoleModelContract) {
                    return $role;
                }

                return app(RoleModelContract::class)->findByName($role);
            });
        }

        return $query->whereHas('roles', function ($query) use ($roles) {
            $query->where(function ($query) use ($roles) {
                foreach ($roles as $role) {
                    $query->orWhere('roles.id', $role->id);
                }
            });
        });
    }

    /**
     * Filter the query excluding the given roles.
     *
     * @param $query
     * @param string|array|RoleModelContract|Collection $roles
     */
    public function scopeWithoutRoles($query, $roles)
    {
        if ($roles instanceof RoleModelContract) {
            $roles = [$roles];
        } else {
            $roles = collect($roles)->map(function ($role) {
                if ($role instanceof RoleModelContract) {
                    return $role;
                }

                return app(RoleModelContract::class)->findByName($role);
            });
        }

        $query->whereDoesntHave('roles', function ($query) use ($roles) {
            $query->where(function ($query) use ($roles) {
                foreach ($roles as $role) {
                    $query->where('roles.id', '=', $role->id);
                }
            });
        });
    }

    /**
     * Filter the query by the given permissions.
     *
     * @param $query
     * @param string|array|RoleModelContract|Collection $permissions
     * @return mixed
     */
    public function scopeWithPermissions($query, $permissions)
    {
        $permissions = $this->convertToPermissionModels($permissions);

        $rolesWithPermissions = array_unique(array_reduce($permissions, function ($result, $permission) {
            return array_merge($result, $permission->roles->all());
        }, []));

        return $query->where(function ($query) use ($permissions, $rolesWithPermissions) {
            $query->whereHas('permissions', function ($query) use ($permissions) {
                $query->where(function ($query) use ($permissions) {
                    foreach ($permissions as $permission) {
                        $query->orWhere('permissions.id', $permission->id);
                    }
                });
            });

            if (count($rolesWithPermissions) > 0) {
                $query->orWhereHas('roles', function ($query) use ($rolesWithPermissions) {
                    $query->where(function ($query) use ($rolesWithPermissions) {
                        foreach ($rolesWithPermissions as $role) {
                            $query->orWhere('roles.id', $role->id);
                        }
                    });
                });
            }
        });
    }

    /**
     * Filter the query excluding the given permissions.
     *
     * @param $query
     * @param string|array|RoleModelContract|Collection $permissions
     * @return mixed
     */
    public function scopeWithoutPermissions($query, $permissions)
    {
        $permissions = $this->convertToPermissionModels($permissions);

        $rolesWithPermissions = array_unique(array_reduce($permissions, function ($result, $permission) {
            return array_merge($result, $permission->roles->all());
        }, []));

        return $query->where(function ($query) use ($permissions, $rolesWithPermissions) {
            $query->whereDoesntHave('permissions', function ($query) use ($permissions) {
                $query->where(function ($query) use ($permissions) {
                    foreach ($permissions as $permission) {
                        $query->orWhere('permissions.id', $permission->id);
                    }
                });
            });

            if (count($rolesWithPermissions) > 0) {
                $query->whereDoesntHave('roles', function ($query) use ($rolesWithPermissions) {
                    $query->where(function ($query) use ($rolesWithPermissions) {
                        foreach ($rolesWithPermissions as $role) {
                            $query->orWhere('roles.id', $role->id);
                        }
                    });
                });
            }
        });
    }

    /**
     * Assign roles to the a user.
     *
     * @param string|array|RoleModelContract|Collection $roles
     * @return $this
     */
    public function assignRoles($roles)
    {
        try {
            if ($roles instanceof RoleModelContract) {
                $this->roles()->save($roles);
            } else {
                $this->roles()->saveMany(
                    collect($roles)->flatten()->map(function ($role) {
                        return is_array($role) || is_a($role, Collection::class) ?
                            app('role.model')->getRoles($role) : app('role.model')->getRole($role);
                    })->all()
                );
            }

            $this->load(['roles', 'permissions']);
        } catch (QueryException $e) {
            $this->removeRoles($roles);
            $this->assignRoles($roles);
        }

        return $this;
    }

    /**
     * Remove roles from the a user.
     *
     * @param string|array|RoleModelContract|Collection $roles
     * @return $this
     */
    public function removeRoles($roles)
    {
        if ($roles instanceof RoleModelContract) {
            $this->roles()->detach($roles);
        } else {
            $this->roles()->detach(
                (new Collection($roles))->map(function ($role) {
                    return is_array($role) || is_a($role, Collection::class) ?
                        app('role.model')->getRoles($role) : app('role.model')->getRole($role);
                })
            );
        }

        $this->load(['roles', 'permissions']);

        return $this;
    }

    /**
     * Sync a user's roles.
     *
     * @param string|array|RoleModelContract|Collection $roles
     * @return $this
     */
    public function syncRoles($roles)
    {
        $this->roles()->detach();
        $this->assignRoles($roles);

        return $this;
    }

    /**
     * Check if a user has a given role.
     *
     * @param string|RoleModelContract $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_numeric($role)) {
            return $this->roles->contains('id', $role);
        }

        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return $this->roles->contains('id', $role->id);
    }

    /**
     * Check if a user has any role from a collection of given roles.
     *
     * @param array|Collection $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        if (!$roles || empty($roles)) {
            return true;
        }

        return (bool)(new Collection($roles))->map(function ($role) {
            return is_array($role) || is_a($role, Collection::class) ?
                app('role.model')->getRoles($role) : app('role.model')->getRole($role);
        })->intersect($this->roles)->count();
    }

    /**
     * Check if a user has every role from a collection of given roles.
     *
     * @param array|Collection $roles
     * @return bool
     */
    public function hasAllRoles($roles)
    {
        $collection = collect()->make($roles)->map(function ($role) {
            return $role instanceof RoleModelContract ? $role->name : $role;
        });

        return $collection == $collection->intersect(
            $this->roles->pluck(is_numeric(Arr::first($roles)) ? 'id' : 'name')
        );
    }

    /**
     * Check if a user has a given permission.
     *
     * @param PermissionModelContract|string|int $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            try {
                $permission = app(PermissionModelContract::class)->findByName($permission);
            } catch (ModelNotFoundException $e) {
                return false;
            }
        }

        return $this->hasDirectPermission($permission) || $this->hasPermissionViaRole($permission);
    }

    /**
     * Check if a user has any permission from a collection of given permissions.
     *
     * @param array|Collection $permissions
     * @return bool
     */
    public function hasAnyPermission($permissions)
    {
        if (!$permissions || empty($permissions)) {
            return true;
        }

        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a user has every permission from a collection of given permissions.
     *
     * @param array|Collection $permissions
     * @return bool
     */
    public function hasAllPermissions($permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check ifa user has a permission whose directly attached to it.
     *
     * @param PermissionModelContract|string|int $permission
     * @return bool
     */
    protected function hasDirectPermission($permission)
    {
        if (is_string($permission)) {
            try {
                $permission = app(PermissionModelContract::class)->findByName($permission);
            } catch (ModelNotFoundException $e) {
                return false;
            }
        }

        if (is_numeric($permission)) {
            try {
                $permission = app(PermissionModelContract::class)->findOrFail($permission);
            } catch (ModelNotFoundException $e) {
                return false;
            }
        }

        return $this->permissions->contains('id', $permission->id);
    }

    /**
     * Check if a user has a permission granted via a role assigned.
     *
     * @param string|PermissionModelContract $permission
     * @return bool
     */
    protected function hasPermissionViaRole($permission)
    {
        if (is_string($permission)) {
            try {
                $permission = app(PermissionModelContract::class)->findByName($permission);
            } catch (ModelNotFoundException $e) {
                return false;
            }
        }

        if (is_numeric($permission)) {
            try {
                $permission = app(PermissionModelContract::class)->findOrFail($permission);
            } catch (ModelNotFoundException $e) {
                return false;
            }
        }

        return $permission->roles->count() > 0 && $this->hasAnyRole($permission->roles);
    }

    /**
     * Get all user's permissions, both direct or via roles.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPermissions()
    {
        return $this->getDirectPermissions()->merge(
            $this->getPermissionsViaRoles()
        )->sort()->values();
    }

    /**
     * Get a user's direct permissions.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getDirectPermissions()
    {
        return $this->permissions;
    }

    /**
     * Get a user's permissions assigned via a role.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getPermissionsViaRoles()
    {
        return $this->load('roles', 'roles.permissions')->roles->flatMap(function ($role) {
            return $role->permissions;
        })->sort()->values();
    }

    /**
     * Convert permissions to Permission models.
     *
     * @param string|array|PermissionModelContract|SupportCollection $permissions
     * @return array
     */
    protected function convertToPermissionModels($permissions)
    {
        if ($permissions instanceof SupportCollection) {
            $permissions = $permissions->toArray();
        }

        $permissions = Arr::wrap($permissions);

        return array_map(function ($permission) {
            if ($permission instanceof PermissionModelContract) {
                return $permission;
            }

            return app(PermissionModelContract::class)->findByName($permission);
        }, $permissions);
    }
}
