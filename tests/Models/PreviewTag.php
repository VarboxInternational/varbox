<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class PreviewTag extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'preview_tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * A tag has and belongs to many posts.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function posts()
    {
        return $this->belongsToMany(PreviewPost::class, 'preview_post_tag', 'tag_id', 'post_id');
    }
}
