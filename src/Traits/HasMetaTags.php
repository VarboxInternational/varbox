<?php

namespace Varbox\Traits;

use Varbox\Contracts\MetaHelperContract;
use Varbox\Options\MetaTagOptions;

trait HasMetaTags
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the Varbox\Options\MetaTagOptions file.
     *
     * @var MetaTagOptions
     */
    protected $metaTagsOptions;

    /**
     * Set the options for the HasSlug trait.
     *
     * @return MetaTagOptions
     */
    abstract public function getMetaTagOptions(): MetaTagOptions;

    /**
     * Return the name of the database column in which to store the meta tags.
     *
     * @return string
     */
    public function getMetaColumn()
    {
        return 'meta';
    }

    /**
     * Display the meta tags stored for a model instance.
     *
     * @return string
     */
    public function displayMetaTags()
    {
        $this->initMetaTagOptions();

        $meta = app(MetaHelperContract::class);
        $tags = $this->{$this->getMetaColumn()};
        $defaults = $this->metaTagsOptions->defaultMetaValues;

        foreach ($tags as $key => $value) {
            if ($value) {
                $meta->set($key, $value);
            } elseif (!empty($defaults[$key])) {
                $meta->set($key, $defaults[$key]);
            } else {
                $meta->set($key, null);
            }
        }

        return $meta->tags();
    }

    /**
     * Instantiate the meta tags options.
     *
     * @return void
     */
    protected function initMetaTagOptions()
    {
        if ($this->metaTagsOptions === null) {
            $this->metaTagsOptions = $this->getMetaTagOptions();
        }
    }
}
