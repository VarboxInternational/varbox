<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Varbox\Options\SlugOptions;
use Varbox\Traits\HasSlug;

class SlugPost extends Model
{
    use HasSlug;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'slug_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'name',
        'slug',
    ];

    /**
     * Get the options for the SlugOptions trait.
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
