<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class SortReview extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'sort_reviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'name',
        'rating',
    ];

    /**
     * A review belongs to a post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(SortPost::class, 'post_id');
    }
}
