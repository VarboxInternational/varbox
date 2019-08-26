<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class DuplicateComment extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'duplicate_comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'title',
        'content',
        'date',
        'votes',
        'active',
    ];

    /**
     * The attributes that should be casted to dates.
     *
     * @var array
     */
    protected $dates = [
        'date',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(DuplicatePost::class, 'post_id');
    }
}
