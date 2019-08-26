<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Varbox\Options\RevisionOptions;
use Varbox\Traits\HasRevisions;

class RevisionPost extends Model
{
    use HasRevisions;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'revision_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author_id',
        'name',
        'content',
        'votes',
        'views',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(RevisionAuthor::class, 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function review()
    {
        return $this->hasOne(RevisionReview::class, 'post_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(RevisionComment::class, 'post_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(RevisionTag::class, 'revision_post_tag', 'post_id', 'tag_id');
    }


    /**
     * @return RevisionOptions
     */
    public function getRevisionOptions()
    {
        return RevisionOptions::instance();
    }
}
