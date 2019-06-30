<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Arr;
use Varbox\Contracts\RoleModelContract;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasPermissions;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class Role extends Model implements RoleModelContract
{
    use HasPermissions;
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table
     *
     * @var string
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'guard',
    ];

    /**
     * Role has and belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        $user = config('varbox.varbox-binding.models.user_model', User::class);

        return $this->belongsToMany($user, 'user_role')->withTimestamps();
    }

    /**
     * Role has and belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        $permission = config('varbox.varbox-binding.models.permission_model', Permission::class);

        return $this->belongsToMany($permission, 'role_permission')->withTimestamps();
    }

    /**
     * Get a role.
     *
     * @param string|array|RoleModelContract|Collection $role
     * @return RoleModelContract|Collection
     */
    public function getRole($role)
    {
        if (is_numeric($role)) {
            return static::findOrFail($role);
        }

        if (is_string($role)) {
            return static::findByName($role);
        }

        return $role;
    }

    /**
     * Get roles.
     *
     * @param array $roles
     * @return Collection
     */
    public static function getRoles($roles)
    {
        return static::whereIn(
            is_numeric(Arr::first($roles)) ? 'id' : 'name', $roles
        )->get();
    }

    /**
     * Return the permission by it's name.
     *
     * @param string $name
     * @return Role
     * @throws ModelNotFoundException
     */
    public static function findByName($name)
    {
        try {
            return static::whereName($name)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Role "' . $name . '" does not exist!');
        }
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('role')
            ->withEntityName($this->name)
            ->withEntityUrl(route('admin.roles.edit', $this->getKey()));
    }
}
