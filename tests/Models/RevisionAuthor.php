<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class RevisionAuthor extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'revision_authors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'name',
        'age',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(RevisionPost::class, 'author_id');
    }
}
