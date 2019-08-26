<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class RevisionReview extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'revision_reviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'name',
        'content',
    ];

    /**
     * A review belongs to a post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(RevisionPost::class, 'post_id');
    }
}
