<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class PreviewPost extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'preview_posts';

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
     * A post has and belongs to many tags.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(PreviewTag::class, 'preview_post_tag', 'post_id', 'tag_id');
    }
}
