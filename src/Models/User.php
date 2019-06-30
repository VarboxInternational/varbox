<?php

namespace Varbox\Models;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Varbox\Contracts\UserModelContract;
use Varbox\Notifications\ResetPassword;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\HasAddresses;
use Varbox\Traits\HasRoles;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class User extends Authenticatable implements UserModelContract
{
    use HasRoles;
    use HasAddresses;
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use Notifiable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'active'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return implode(' ', [$this->first_name, $this->last_name]);
    }

    /**
     * Sort query results in alphabetical order by email.
     *
     * @param Builder $query
     */
    public function scopeAlphabetically($query)
    {
        $query->orderBy('email');
    }

    /**
     * Filter query results to show only active users.
     *
     * @param Builder $query
     */
    public function scopeOnlyActive($query)
    {
        $query->where('active', true);
    }

    /**
     * Filter query results to show only inactive users.
     *
     * @param Builder $query
     */
    public function scopeOnlyInactive($query)
    {
        $query->where('active', false);
    }

    /**
     * Filter query results to show only admin users.
     *
     * @param Builder $query
     */
    public function scopeOnlyAdmins($query)
    {
        $query->withRoles('Admin');
    }

    /**
     * Filter query results to exclude admin users.
     *
     * @param Builder $query
     */
    public function scopeExcludingAdmins($query)
    {
        $query->withoutRoles('Admin');
    }

    /**
     * Filter query results to show only super users.
     *
     * @param Builder $query
     */
    public function scopeOnlySuper($query)
    {
        $query->withRoles('Super');
    }

    /**
     * Filter query results to exclude super users.
     *
     * @param Builder $query
     */
    public function scopeExcludingSuper($query)
    {
        $query->withoutRoles('Super');
    }

    /**
     * Determine if the current user is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->active == true;
    }

    /**
     * Determine if the current user is inactive.
     *
     * @return bool
     */
    public function isInactive()
    {
        return !$this->isActive();
    }

    /**
     * Determine if the current user is an admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    /**
     * Determine if the current user is a super user.
     *
     * @return bool
     */
    public function isSuper()
    {
        return $this->hasRole('Super');
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getKeyName()};
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->getTable() . '.' . $this->getKeyName();
    }

    /**
     * Override route model binding default column value.
     * This is done because the user is joined with person by the global scope.
     * Otherwise, the model binding will throw an "ambiguous column" error.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return $this->getTable() . '.' . $this->getKeyName();
    }

    /**
     * Send the password reset email.
     * Determine if user requesting the password is an admin or not.
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        if ($this->isAdmin()) {
            $type = 'admin';
            $url = route('admin.admins.edit', $this->getKey());
        } else {
            $type = 'user';
            $url = route('admin.users.edit', $this->getKey());
        }

        return ActivityOptions::instance()
            ->withEntityType($type)
            ->withEntityName($this->email)
            ->withEntityUrl($url);
    }
}
