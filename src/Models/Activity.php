<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User;
use Varbox\Contracts\ActivityModelContract;
use Varbox\Contracts\UserModelContract;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class Activity extends Model implements ActivityModelContract
{
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'activity';

    /**
     * The attributes that are protected against mass assign.
     *
     * @var array
     */
    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'obsolete' => 'boolean',
    ];

    /**
     * The relations that are eager-loaded.
     *
     * @var array
     */
    protected $with = [
        'user',
    ];

    /**
     * Activity belongs to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $user = config('varbox.varbox-bindings.models.user_model', User::class);

        return $this->belongsTo($user, 'user_id');
    }

    /**
     * Activity belongs to a subject.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Get a pretty formatted message for an activity log entry.
     *
     * @return string
     */
    public function getMessageAttribute()
    {
        $message = [];

        $message[] = '<strong>' . (optional($this->user)->email ?: 'A user') . '</strong>';
        $message[] = $this->event;
        $message[] = 'a ' . $this->entity_type ?: $this->subject->getMorphClass();

        if (!$this->obsolete && $this->entity_url) {
            $message[] = '(<a href="' . url($this->entity_url) . '" target="_blank">' . $this->entity_name . '</a>)';
        } else {
            $message[] = '(<span style="color: #868e96;">' . $this->entity_name . '</span>)';
        }

        return implode(' ', $message);
    }

    /**
     * Filter the query to only include activities by a given causer.
     *
     * @param Builder $query
     * @param UserModelContract $user
     * @return mixed
     */
    public function scopeCausedBy($query, UserModelContract $user)
    {
        return $query->whereUserId($user->getKey());
    }

    /**
     * Filter the query to only include activities for a given subject.
     *
     * @param Builder $query
     * @param Model $subject
     * @return mixed
     */
    public function scopeForSubject($query, Model $subject)
    {
        return $query->where([
            'subject_id' => $subject->getKey(),
            'subject_type' => $subject->getMorphClass(),
        ]);
    }

    /**
     * Get all distinct event values from the activity table.
     *
     * @return array
     */
    public static function getDistinctEvents()
    {
        return static::withoutGlobalScopes()
            ->select('event')
            ->groupBy('event')
            ->pluck('event', 'event')
            ->toArray();
    }

    /**
     * Get all distinct entity type values from the activity table.
     *
     * @return array
     */
    public static function getDistinctEntities()
    {
        return static::withoutGlobalScopes()
            ->select('entity_type')
            ->groupBy('entity_type')
            ->pluck('entity_type', 'entity_type')
            ->toArray();
    }

    /**
     * Attempt to clean old activity.
     *
     * Activity qualifies as being old if:
     * "created_at" field is smaller than the current date minus the number of days set in the
     * "delete_records_older_than" key of /config/varbox/audit/activity.php file.
     *
     * @return void
     */
    public static function deleteOld()
    {
        if (($days = (int)config('varbox.varbox-activity.delete_records_older_than', 30)) && $days > 0) {
            static::where('created_at', '<', today()->subDays($days))->delete();
        }
    }
}
