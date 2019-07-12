<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Varbox\Contracts\ConfigModelContract;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class Config extends Model implements ConfigModelContract
{
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'configs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get the value attribute.
     *
     * @return array|string
     */
    public function getValueAttribute()
    {
        if (Str::contains($this->attributes['value'], ';')) {
            return explode(';', $this->attributes['value']);
        }

        return $this->attributes['value'];
    }

    /**
     * Get all the configuration keys that are allowed to be editable.
     *
     * @return array
     */
    public static function getAllowedKeys()
    {
        $keys = [];

        foreach (config('varbox.config.keys', []) as $key) {
            $keys[$key] = ucwords(str_replace('.', ' ', $key));
        }

        return $keys;
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('config')
            ->withEntityName($this->key)
            ->withEntityUrl(route('admin.configs.edit', $this->getKey()));
    }
}