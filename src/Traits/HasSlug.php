<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Varbox\Options\SlugOptions;

trait HasSlug
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the Varbox\Options\SlugOptions file.
     *
     * @var SlugOptions
     */
    protected $slugOptions;

    /**
     * Set the options for the HasSlug trait.
     *
     * @return SlugOptions
     */
    abstract public function getSlugOptions(): SlugOptions;

    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootHasSlug()
    {
        static::creating(function (Model $model) {
            $model->generateSlugOnCreate();
        });

        static::updating(function (Model $model) {
            $model->generateSlugOnUpdate();
        });
    }

    /**
     * Handle setting the slug on model creation.
     *
     * @return void
     * @throws Exception
     */
    protected function generateSlugOnCreate()
    {
        $this->initSlugOptions();

        if ($this->slugOptions->generateSlugOnCreate === false) {
            return;
        }

        $this->generateSlug();
    }

    /**
     * Handle setting the slug on model update.
     *
     * @return void
     * @throws Exception
     */
    protected function generateSlugOnUpdate()
    {
        $this->initSlugOptions();

        if ($this->slugOptions->generateSlugOnUpdate === false) {
            return;
        }

        $this->generateSlug();
    }

    /**
     * The logic for actually setting the slug.
     *
     * @return void
     * @throws Exception
     */
    public function generateSlug()
    {
        $this->initSlugOptions();

        if ($this->slugHasBeenSupplied()) {
            $slug = $this->generateNonUniqueSlug();

            if ($this->slugOptions->uniqueSlugs) {
                $slug = $this->makeSlugUnique($slug);
            }

            $this->setAttribute($this->slugOptions->toField, $slug);
        }
    }

    /**
     * Generate a non unique slug for this record.
     *
     * @return string
     */
    protected function generateNonUniqueSlug()
    {
        if ($this->slugHasChanged()) {
            $source = $this->getAttribute($this->slugOptions->toField);

            return Str::is('/', $source) ? $source : Str::slug($source);
        }

        $source = $this->getSlugSource();

        return Str::is('/', $source) ? $source : Str::slug(
            $source, $this->slugOptions->slugSeparator, $this->slugOptions->slugLanguage
        );
    }

    /**
     * Make the given slug unique.
     *
     * @param string $slug
     * @return string
     */
    protected function makeSlugUnique($slug)
    {
        $original = $slug;
        $i = 1;

        while ($this->slugAlreadyExists($slug) || $slug === '') {
            $slug = $original.$this->slugOptions->slugSeparator.$i++;
        }

        return $slug;
    }

    /**
     * Check if the $fromField slug has been supplied.
     * If not, then skip the entire slug generation.
     *
     * @return bool
     */
    protected function slugHasBeenSupplied()
    {
        if (is_array($this->slugOptions->fromField)) {
            foreach ($this->slugOptions->fromField as $field) {
                if ($this->getAttribute($field) !== null) {
                    return true;
                }
            }

            return false;
        }

        return $this->getAttribute($this->slugOptions->fromField) !== null;
    }

    /**
     * Determine if a custom slug has been saved.
     *
     * @return bool
     */
    protected function slugHasChanged()
    {
        return
            $this->getOriginal($this->slugOptions->toField) &&
            $this->getOriginal($this->slugOptions->toField) != $this->getAttribute($this->slugOptions->toField);
    }

    /**
     * Get the string that should be used as base for the slug.
     *
     * @return string
     */
    protected function getSlugSource()
    {
        if (is_callable($this->slugOptions->fromField)) {
            return call_user_func($this->slugOptions->fromField, $this);
        }

        return collect($this->slugOptions->fromField)->map(function ($field) {
            return $this->getAttribute($field) ?: '';
        })->implode($this->slugOptions->slugSeparator);
    }

    /**
     * Check if the given slug already exists on another record.
     *
     * @param string $slug
     * @return bool
     */
    protected function slugAlreadyExists($slug)
    {
        return (bool) static::withoutGlobalScopes()->where($this->slugOptions->toField, $slug)
            ->where($this->getKeyName(), '!=', $this->getKey() ?: '0')
            ->first();
    }

    /**
     * Both instantiate the slug options as well as validate their contents.
     *
     * @return void
     * @throws Exception
     */
    protected function initSlugOptions()
    {
        if ($this->slugOptions === null) {
            $this->slugOptions = $this->getSlugOptions();
        }

        $this->validateSlugOptions();
    }

    /**
     * Check if mandatory slug options have been properly set from the model.
     * Check if $fromField and $toField have been set.
     *
     * @return void
     * @throws Exception
     */
    protected function validateSlugOptions()
    {
        if (! $this->slugOptions->fromField) {
            throw new Exception(
                'The model ' . static::class . ' uses the HasSlug trait'.PHP_EOL.
                'You are required to set the field from where to generate the slug ($fromField)'.PHP_EOL.
                'You can do this from inside the getSlugOptions() method defined on the model.'
            );
        }

        if (! $this->slugOptions->toField) {
            throw new Exception(
                'The model ' . static::class . ' uses the HasSlug trait'.PHP_EOL.
                'You are required to set the field where to store the generated slug ($toField)'.PHP_EOL.
                'You can do this from inside the getSlugOptions() method defined on the model.'
            );
        }
    }
}
