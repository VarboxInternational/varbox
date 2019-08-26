<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class RevisionTag extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'revision_tags';

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
        return $this->belongsToMany(RevisionPost::class, 'revision_post_tag', 'tag_id', 'post_id');
    }
}
