<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Varbox\Options\BlockOptions;
use Varbox\Traits\HasBlocks;

class BlockAuthor extends Model
{
    use HasBlocks;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'block_authors';

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
        return $this->hasMany(BlockPost::class, 'author_id');
    }

    /**
     * Set the options for the HasBlocks trait.
     *
     * @return BlockOptions
     */
    public function getBlockOptions()
    {
        return BlockOptions::instance()
            ->withLocations(['header', 'content']);
    }
}
