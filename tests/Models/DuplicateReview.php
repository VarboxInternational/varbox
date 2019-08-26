<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class DuplicateReview extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'duplicate_reviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'name',
        'content',
        'rating',
    ];

    /**
     * A review belongs to a post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(DuplicatePost::class, 'post_id');
    }
}
