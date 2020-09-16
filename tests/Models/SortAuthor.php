<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class SortAuthor extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'sort_authors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'age',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(SortPost::class, 'author_id');
    }
}
