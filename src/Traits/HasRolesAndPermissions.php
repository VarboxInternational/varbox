<?php

namespace Varbox\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Str;
use Varbox\Contracts\PermissionModelContract;
use Varbox\Contracts\RoleModelContract;
use Varbox\Models\Permission;
use Varbox\Models\Role;

trait HasRolesAndPermissions
{
    /**
     * @var SupportCollection
     */
    protected $allowedDirectPermissions;

    /**
     * @var SupportCollection
     */
    protected $allowedPermissionsViaRoles;

    /**
     * A user belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        $role = config('varbox.bindings.models.role_model', Role::class);

        return $this->belongsToMany($role, 'user_role')->withTimestamps();
    }

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
     * Filter the query by the given roles.
     *
     * @param $query
     * @param int|string|array|RoleModelContract|Collection $roles
     * @return mixed
     */
    public function scopeWithRoles($query, $roles)
    {
        $roles = $this->convertToRoleModels($roles);

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
     * @param int|string|array|RoleModelContract|Collection $roles
     */
    public function scopeWithoutRoles($query, $roles)
    {
        $roles = $this->convertToRoleModels($roles);

        $query->whereDoesntHave('roles', function ($query) use ($roles) {
            $query->where(function ($query) use ($roles) {
                foreach ($roles as $role) {
                    $query->orWhere('roles.id', '=', $role->id);
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

        return $query->whereHas('permissions', function ($query) use ($permissions) {
            $query->where(function ($query) use ($permissions) {
                foreach ($permissions as $permission) {
                    $query->orWhere('permissions.id', $permission->id);
                }
            });
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

        return $query->where(function ($query) use ($permissions) {
            $query->whereDoesntHave('permissions', function ($query) use ($permissions) {
                $query->where(function ($query) use ($permissions) {
                    foreach ($permissions as $permission) {
                        $query->orWhere('permissions.id', $permission->id);
                    }
                });
            });
        });
    }

    /**
     * @return array
     */
    public function getAllGuards()
    {
        $guards = [];

        foreach (config('auth.guards') as $guard => $options) {
            $guards[$guard] = Str::title($guard);
        }

        return $guards;
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
                            app(RoleModelContract::class)->getRoles($role) : app(RoleModelContract::class)->getRole($role);
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
                        app(RoleModelContract::class)->getRoles($role) : app(RoleModelContract::class)->getRole($role);
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
     * Check if a user has a given role.
     *
     * @param RoleModelContract|string|int $role
     * @return bool
     */
    public function hasRole($role)
    {
        if ($role instanceof RoleModelContract) {
            return $this->roles->contains($role->getKeyName(), $role->getKey());
        }

        if (is_numeric($role)) {
            return $this->roles->contains('id', $role);
        }

        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return false;
    }

    /**
     * Check if a user has any role from a collection of given roles.
     *
     * @param Collection|array $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        if (!$roles || empty($roles)) {
            return true;
        }

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a user has every role from a collection of given roles.
     *
     * @param array|Collection $roles
     * @return bool
     */
    public function hasAllRoles($roles)
    {
        if (!$roles || empty($roles)) {
            return true;
        }

        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a user has a given permission.
     *
     * @param PermissionModelContract|string|int $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
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
        if (!$permissions || empty($permissions)) {
            return true;
        }

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
        if (!$this->allowedDirectPermissions) {
            $this->allowedDirectPermissions = $this->getDirectPermissions();
        }

        if ($permission instanceof PermissionModelContract) {
            return $this->allowedDirectPermissions
                ->pluck($permission->getKeyName())->contains($permission->getKey());
        }

        if (is_string($permission)) {
            return $this->allowedDirectPermissions
                ->pluck('name')->contains($permission);
        }

        if (is_numeric($permission)) {
            return $this->allowedDirectPermissions
                ->pluck('id')->contains($permission);
        }

        return false;
    }

    /**
     * Check if a user has a permission granted via a role assigned.
     *
     * @param string|PermissionModelContract $permission
     * @return bool
     */
    protected function hasPermissionViaRole($permission)
    {
        if (!$this->allowedPermissionsViaRoles) {
            $this->allowedPermissionsViaRoles = $this->getPermissionsViaRoles();
        }

        if ($permission instanceof PermissionModelContract) {
            return $this->allowedPermissionsViaRoles
                ->pluck($permission->getKeyName())->contains($permission->getKey());
        }

        if (is_string($permission)) {
            return $this->allowedPermissionsViaRoles
                ->pluck('name')->contains($permission);
        }

        if (is_numeric($permission)) {
            return $this->allowedPermissionsViaRoles
                ->pluck('id')->contains($permission);
        }

        return false;
    }

    /**
     * Get all user's permissions, both direct or via roles.
     *
     * @return SupportCollection
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
     * @return SupportCollection
     */
    protected function getDirectPermissions()
    {
        return $this->permissions;
    }

    /**
     * Get a user's permissions assigned via a role.
     *
     * @return SupportCollection
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
     * @param string|array|RoleModelContract|SupportCollection $roles
     * @return array
     */
    protected function convertToRoleModels($roles)
    {
        if ($roles instanceof RoleModelContract) {
            return [$roles];
        }

        return collect($roles)->map(function ($role) {
            return app(RoleModelContract::class)->getRole($role);
        });
    }

    /**
     * Convert permissions to Permission models.
     *
     * @param string|array|PermissionModelContract|SupportCollection $permissions
     * @return array
     */
    protected function convertToPermissionModels($permissions)
    {
        if ($permissions instanceof PermissionModelContract) {
            return [$permissions];
        }

        return collect($permissions)->map(function ($permission) {
            return app(PermissionModelContract::class)->getPermission($permission);
        });
    }
}
