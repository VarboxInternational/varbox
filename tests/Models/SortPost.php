<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Varbox\Traits\IsSortable;

class SortPost extends Model
{
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'sort_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author_id',
        'name',
        'views',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(SortAuthor::class, 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function review()
    {
        return $this->hasOne(SortReview::class, 'post_id');
    }
}
