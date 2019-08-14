<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Varbox\Models\Url;
use Varbox\Options\SlugOptions;
use Varbox\Options\UrlOptions;

trait HasUrl
{
    use HasSlug;

    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the Varbox\Options\UrlOptions file.
     *
     * @var UrlOptions
     */
    protected $urlOptions;

    /**
     * Set the options for the HasUrl trait.
     *
     * @return UrlOptions
     */
    abstract public function getUrlOptions(): UrlOptions;

    /**
     * Flag to manually enable/disable the url generation only for the current request.
     *
     * @var bool
     */
    protected static $generateUrl = true;

    /**
     * Boot the trait.
     *
     * Check if the "getUrlOptions" method has been implemented on the underlying model class.
     * Eager load urls through anonymous global scope.
     * Trigger eloquent events to create, update, delete url.
     *
     * @return void
     */
    public static function bootHasUrl()
    {
        static::addGlobalScope('url', function (Builder $builder) {
            $builder->with('url');
        });

        static::created(function (Model $model) {
            if (self::$generateUrl === true) {
                $model->createUrl();
            }
        });

        static::updated(function (Model $model) {
            if (self::$generateUrl === true) {
                $model->updateUrl();
            }
        });

        static::saved(function (Model $model) {
            if (self::$generateUrl === false) {
                self::$generateUrl = true;
            }
        });

        static::deleted(function (Model $model) {
            if ($model->forceDeleting !== false) {
                $model->deleteUrl();
            }
        });
    }

    /**
     * Get the model's url.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function url()
    {
        return $this->morphOne(Url::class, 'urlable');
    }

    /**
     * Get the model's direct url string.
     *
     * @param bool|null $secure
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string|null
     */
    public function getUrl($secure = null)
    {
        if ($this->url && $this->url->exists) {
            return url($this->url->url, [], $secure);
        }

        return null;
    }

    /**
     * Get the model's direct uri string.
     *
     * @return string|null
     */
    public function getUri()
    {
        return optional($this->url)->url ?: null;
    }

    /**
     * Disable the url generation manually only for the current request.
     *
     * @return static
     */
    public function doNotGenerateUrl()
    {
        self::$generateUrl = false;

        return $this;
    }

    /**
     * Get the options for the HasSlug trait.
     *
     * @return SlugOptions
     * @throws Exception
     */
    public function getSlugOptions(): SlugOptions
    {
        $this->initUrlOptions();

        return SlugOptions::instance()
            ->generateSlugFrom($this->urlOptions->fromField)
            ->saveSlugTo($this->urlOptions->toField);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function saveUrl()
    {
        $this->initUrlOptions();

        if ($this->url && $this->url->exists) {
            $this->updateUrl();
        } else {
            $this->createUrl();
        }
    }

    /**
     * Create a new url for the model.
     *
     * @return void
     * @throws Exception
     */
    public function createUrl()
    {
        $this->initUrlOptions();

        if (! $this->getAttribute($this->urlOptions->toField)) {
            return;
        }

        $this->url()->create([
            'url' => $this->buildFullUrl(),
        ]);
    }

    /**
     * Update the existing url for the model.
     *
     * @return void
     * @throws Exception
     */
    public function updateUrl()
    {
        $this->initUrlOptions();

        if (! $this->getAttribute($this->urlOptions->toField)) {
            return;
        }

        DB::transaction(function () {
            if ($this->url()->count() == 0) {
                $this->createUrl();
            }

            $this->url()->update([
                'url' => $this->buildFullUrl(),
            ]);

            if ($this->urlOptions->cascadeUpdate === true) {
                $this->updateUrlsInCascade();
            }
        });
    }

    /**
     * Delete the url for the just deleted model.
     *
     * @return void
     */
    public function deleteUrl()
    {
        $this->url()->delete();
    }

    /**
     * Synchronize children urls for the actual model's url.
     * Saves all children urls of the model in use with the new parent model's slug.
     *
     * @return void
     */
    protected function updateUrlsInCascade()
    {
        $old = trim($this->getOriginal($this->urlOptions->toField), '/');
        $new = trim($this->getAttribute($this->urlOptions->toField), '/');

        $children = URL::where('urlable_type', static::class)->where(function ($query) use ($old) {
            $query->where('url', 'like', "{$old}/%")->orWhere('url', 'like', "%/{$old}/%");
        })->get();

        foreach ($children as $child) {
            $child->update([
                'url' => str_replace($old.'/', $new.'/', $child->url),
            ]);
        }
    }

    /**
     * Get the full relative url.
     * The full url will also include the prefix and suffix if any was provided.
     *
     * @return string
     */
    protected function buildFullUrl()
    {
        $prefix = $this->buildUrlSegment('prefix');
        $suffix = $this->buildUrlSegment('suffix');

        return
            (Str::is('/', $prefix) ? '' : ($prefix ? $prefix.$this->urlOptions->urlGlue : '')).
            $this->getAttribute($this->urlOptions->toField).
            (Str::is('/', $suffix) ? '' : ($suffix ? $this->urlOptions->urlGlue.$suffix : ''));
    }

    /**
     * Build the url segment.
     * This can be either "prefix" or "suffix".
     * The accepted parameter $type accepts only "prefix" and "suffix" as it's value.
     * Otherwise, the method will return an empty string.
     *
     * @param string $type
     * @return string
     */
    protected function buildUrlSegment($type)
    {
        if ($type != 'prefix' && $type != 'suffix') {
            return '';
        }

        $segment = $this->urlOptions->{'url'.ucwords($type)};

        if (is_callable($segment)) {
            return call_user_func_array($segment, [[], $this]);
        } elseif (is_array($segment)) {
            return implode($this->urlOptions->urlGlue, $segment);
        } elseif (is_string($segment)) {
            return $segment;
        } else {
            return '';
        }
    }

    /**
     * Both instantiate the url options as well as validate their contents.
     *
     * @return void
     * @throws Exception
     */
    protected function initUrlOptions()
    {
        if ($this->urlOptions === null) {
            $this->urlOptions = $this->getUrlOptions();
        }

        $this->validateUrlOptions();
    }

    /**
     * Check if mandatory slug options have been properly set from the model.
     * Check if $fromField and $toField have been set.
     *
     * @return void
     * @throws Exception
     */
    protected function validateUrlOptions()
    {
        if (! $this->urlOptions->routeController || ! $this->urlOptions->routeAction) {
            throw new Exception(
                'The model '.static::class.' uses the HasUrl trait'.PHP_EOL.
                'You are required to set the routing from where Laravel will dispatch it\'s route requests.'.PHP_EOL.
                'You can do this from inside the getUrlOptions() method defined on the model.'
            );
        }

        if (! $this->urlOptions->fromField) {
            throw new Exception(
                'The model '.static::class.' uses the HasUrl trait'.PHP_EOL.
                'You are required to set the field from where to generate the url slug ($fromField)'.PHP_EOL.
                'You can do this from inside the getUrlOptions() method defined on the model.'
            );
        }

        if (! $this->urlOptions->toField) {
            throw new Exception(
                'The model '.static::class.' uses the HasUrl trait'.PHP_EOL.
                'You are required to set the field where to store the generated url slug ($toField)'.PHP_EOL.
                'You can do this from inside the getUrlOptions() method defined on the model.'
            );
        }
    }
}
