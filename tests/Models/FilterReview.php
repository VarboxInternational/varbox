<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class FilterReview extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'filter_reviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'name',
    ];

    /**
     * A review belongs to a post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(FilterPost::class, 'post_id');
    }
}
