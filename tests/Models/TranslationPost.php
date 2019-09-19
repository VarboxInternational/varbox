<?php

namespace Varbox\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Varbox\Options\TranslationOptions;
use Varbox\Traits\HasTranslations;

class TranslationPost extends Model
{
    use HasTranslations;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'translation_posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'content',
        'data',
    ];

    /**
     * The attributes that should be cased to their native types.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array'
    ];

    /**
     * Get the options for the TranslationOptions trait.
     *
     * @return TranslationOptions
     */
    public function getTranslationOptions() : TranslationOptions
    {
        return TranslationOptions::instance()
            ->fieldsToTranslate('name', 'content', 'data');
    }
}
