<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Varbox\Traits\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Request;
use Varbox\Contracts\UrlModelContract;

class Url extends Model implements UrlModelContract
{
    use HasFactory;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'urls';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'url',
        'urlable_id',
        'urlable_type',
    ];

    /**
     * Get all of the owning urlable models.
     *
     * @return MorphTo
     */
    public function urlable() : MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Filter the query by the urlable morph relation.
     *
     * @param Builder $query
     * @param int $id
     * @param string $type
     */
    public function scopeWhereUrlable($query, $id, $type)
    {
        $query->where([
            'urlable_id' => $id,
            'urlable_type' => $type,
        ]);
    }

    /**
     * Sort the query alphabetically by url.
     *
     * @param Builder $query
     */
    public function scopeAlphabetically($query)
    {
        $query->orderBy('url', 'asc');
    }

    /**
     * Get the model instance correlated with the accessed url.
     *
     * @param bool $silent
     * @return Model|null
     * @throws ModelNotFoundException
     */
    public static function findUrlable($silent = true)
    {
        $model = Request::route()->action['model'] ?? null;

        if ($model && $model instanceof Model && $model->exists) {
            return $model;
        }

        if ($silent === false) {
            throw new ModelNotFoundException;
        }
    }

    /**
     * Get the model instance correlated with the accessed url.
     * Throw a ModelNotFoundException if the model doesn't exist.
     *
     * @return Model|null
     * @throws ModelNotFoundException
     */
    public static function findUrlableOrFail()
    {
        return static::findUrlable(false);
    }
}
