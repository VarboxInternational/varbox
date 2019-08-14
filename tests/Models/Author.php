<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Varbox\Options\SlugOptions;
use Varbox\Traits\HasSlug;

class Author extends Model
{
    use HasSlug;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'test_authors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'name',
        'age',
        'slug'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    /**
     * Get the options for the HasSlug trait.
     *
     * @return SlugOptions
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::instance()
            ->generateSlugFrom('name')
            ->saveSlugTo('slug');
    }
}
