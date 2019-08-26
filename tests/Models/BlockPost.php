<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Varbox\Options\BlockOptions;
use Varbox\Traits\HasBlocks;

class BlockPost extends Model
{
    use HasBlocks;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'block_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'author_id',
        'name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(BlockAuthor::class, 'author_id');
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
