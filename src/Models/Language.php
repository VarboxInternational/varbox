<?php

namespace Varbox\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Varbox\Contracts\LanguageModelContract;
use Varbox\Exceptions\CrudException;
use Varbox\Options\ActivityOptions;
use Varbox\Traits\HasActivity;
use Varbox\Traits\IsCacheable;
use Varbox\Traits\IsCsvExportable;
use Varbox\Traits\IsFilterable;
use Varbox\Traits\IsSortable;

class Language extends Model implements LanguageModelContract
{
    use HasFactory;
    use HasActivity;
    use IsCacheable;
    use IsFilterable;
    use IsSortable;
    use IsCsvExportable;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'languages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'default',
        'active',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'default' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * Boot the model.
     *
     * When a language is set as default, set all other languages as non-default.
     *
     * When deleting a language, check if the language is the default one.
     * If it is, send an error that will be parsed in the controller for the user.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->getOriginal('default') == true && $model->getAttribute('default') == false) {
                throw new CrudException('A default language is required at all times!');
            }

            if ($model->isDirty('default') && $model->getAttribute('default') == true) {
                static::where($model->getKeyName(), '!=', $model->getKey())->update([
                    'default' => false
                ]);
            }

            return true;
        });

        static::deleting(function ($model) {
            if ($model->getAttribute('default') == true) {
                throw new CrudException('Deleting the default language is restricted!');
            }

            return true;
        });
    }

    /**
     * Filter the query to show only default results.
     *
     * @param Builder $query
     */
    public function scopeOnlyDefault($query)
    {
        $query->where('default', true);
    }

    /**
     * Filter the query to show only non-default results.
     *
     * @param Builder $query
     */
    public function scopeExcludingDefault($query)
    {
        $query->where('default', false);
    }

    /**
     * Filter the query to return only active results.
     *
     * @param Builder $query
     */
    public function scopeOnlyActive($query)
    {
        $query->where('active', true);
    }

    /**
     * Filter the query to return only inactive results.
     *
     * @param Builder $query
     */
    public function scopeOnlyInactive($query)
    {
        $query->where('active', false);
    }

    /**
     * Sort the query alphabetically by name.
     *
     * @param Builder $query
     */
    public function scopeAlphabetically($query)
    {
        $query->orderBy('name', 'asc');
    }

    /**
     * Set the options for the HasActivity trait.
     *
     * @return ActivityOptions
     */
    public function getActivityOptions()
    {
        return ActivityOptions::instance()
            ->withEntityType('language')
            ->withEntityName($this->name)
            ->withEntityUrl(route('admin.languages.edit', $this->getKey()));
    }

    /**
     * Get the heading columns for the csv.
     *
     * @return array
     */
    public function getCsvColumns()
    {
        return [
            'Name', 'Code', 'Default', 'Active', 'Created At', 'Last Modified At',
        ];
    }

    /**
     * Get the values for a row in the csv.
     *
     * @return array
     */
    public function toCsvArray()
    {
        return [
            $this->name,
            strtoupper($this->code),
            $this->default ? 'Yes' : 'No',
            $this->active ? 'Yes' : 'No',
            $this->created_at->format('Y-m-d H:i:s'),
            $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
