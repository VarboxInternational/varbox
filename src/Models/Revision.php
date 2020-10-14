<?php

namespace Varbox\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Varbox\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Varbox\Contracts\RevisionModelContract;
use Varbox\Traits\IsDraftable;

class Revision extends Model implements RevisionModelContract
{
    use HasFactory;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'revisions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'revisionable_id',
        'revisionable_type',
        'data',
    ];

    /**
     * The attributes that are casted to a specific type.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Revision belongs to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        $user = config('varbox.bindings.models.user_model', User::class);

        return $this->belongsTo($user, 'user_id');
    }

    /**
     * Get all of the owning revisionable models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function revisionable()
    {
        $morph = $this->morphTo();
        $related = $morph->getRelated();

        if (in_array(SoftDeletes::class, class_uses($related))) {
            $morph->withTrashed();
        }

        if (in_array(IsDraftable::class, class_uses($related))) {
            $morph->withDrafts();
        }

        return $morph;
    }

    /**
     * Filter the query by the given user id.
     *
     * @param Builder $query
     * @param Authenticatable|int $user
     */
    public function scopeOfUser($query, $user)
    {
        $query->where('user_id', $user instanceof Authenticatable ? $user->id : $user);
    }

    /**
     * Filter the query by the given revisionable params (id, type).
     *
     * @param Builder $query
     * @param int $id
     * @param string $type
     */
    public function scopeWhereRevisionable($query, $id, $type)
    {
        $query->where([
            'revisionable_id' => $id,
            'revisionable_type' => $type,
        ]);
    }
}
