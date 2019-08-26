<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;

class ActivityPost extends Model
{
    use HasActivity;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'activity_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('post')
            ->withEntityName($this->name)
            ->withEntityUrl($this->slug);
    }
}
