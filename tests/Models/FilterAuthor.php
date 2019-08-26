<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;

class FilterAuthor extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'filter_authors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(FilterPost::class, 'author_id');
    }
}
