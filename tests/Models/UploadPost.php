<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Varbox\Traits\HasUploads;

class UploadPost extends Model
{
    use HasUploads;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'upload_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image',
        'video',
        'audio',
        'file',
    ];

    /**
     * Get the specific upload config parts for this model.
     *
     * @return array
     */
    public function getUploadConfig()
    {
        return [];
    }
}
