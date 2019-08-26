<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Varbox\Traits\IsCacheable;

class CacheComment extends Model
{
    use IsCacheable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'cache_comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'title',
        'content',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(CachePost::class, 'post_id');
    }
}
