<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class FilterComment extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'filter_comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'title',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(FilterPost::class, 'post_id');
    }
}
