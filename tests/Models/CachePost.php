<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Varbox\Traits\IsCacheable;

class CachePost extends Model
{
    use IsCacheable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'cache_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'content',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(CacheComment::class, 'post_id');
    }
}
