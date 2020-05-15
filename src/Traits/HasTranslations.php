<?php

namespace Varbox\Traits;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Varbox\Options\TranslationOptions;

trait HasTranslations
{
    /**
     * The container for all the options necessary for this trait.
     * Options can be viewed in the Varbox\Options\TranslationOptions file.
     *
     * @var TranslationOptions
     */
    protected $translationOptions;

    /**
     * Set the options for the HasSlug trait.
     *
     * @return TranslationOptions
     */
    abstract public function getTranslationOptions(): TranslationOptions;

    /**
     * Get an attribute's translatable value.
     *
     * @param string $key
     * @return string
     * @throws Exception
     */
    public function getAttribute($key)
    {
        if ($this->isTranslatableAttribute($key)) {
            return $this->getTranslation($key, app()->getLocale());
        }

        return parent::getAttribute($key);
    }

    /**
     * Set an attribute's translatable value.
     *
     * @param string $key
     * @param array|string $value
     * @return $this
     * @throws Exception
     */
    public function setAttribute($key, $value)
    {
        if (!($this->isTranslatableAttribute($key) && $this->exists === true)) {
            return parent::setAttribute($key, $value);
        }

        $this->setTranslation($key, $value, app()->getLocale());

        return $this;
    }

    /**
     * @param string $key
     * @param string|null $locale
     * @return string
     * @throws Exception
     */
    public function translate($key, $locale)
    {
        return $this->getTranslation($key, $locale);
    }

    /**
     * Get the value for a key for the specified locale.
     *
     * @param string $key
     * @param string $locale
     * @param bool $useFallbackLocale
     * @return string
     * @throws Exception
     */
    public function getTranslation($key, $locale, $useFallbackLocale = true)
    {
        $field = strtok($key, '.[]->');
        $locale = $this->normalizeLocale($key, $locale, $useFallbackLocale);
        $translation = $this->getTranslations($field)[$locale] ?? '';

        if (Str::is('*[*]*', $key) && (is_array($translation) || is_object($translation))) {
            return Arr::get(
                is_object($translation) ? get_object_vars_recursive($translation) : $translation,
                trim(str_replace($field, '', str_replace(['][', '[', ']'], '.', trim($key, '.[]'))), '.[]')
            );
        }

        if ($this->hasGetMutator($key)) {
            return $this->mutateAttribute($key, $translation);
        }

        return $translation;
    }

    /**
     * Get all translations for all locales for the specified key.
     *
     * @param string $key
     * @return mixed
     * @throws Exception
     */
    public function getTranslations($key)
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        return $this->fromJson($this->attributes[$key] ?? '' ?: '{}');
    }

    /**
     * Set the translatable value for a key of the specified locale.
     *
     * @param string $key
     * @param string $locale
     * @param string $value
     * @return $this
     * @throws Exception
     */
    public function setTranslation($key, $value, $locale)
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        if (is_array($value) && isset($value[app()->getLocale()])) {
            $value = $value[app()->getLocale()];
        }

        if ($this->hasSetMutator($key)) {
            $this->setMutatedAttributeValue($key, $value);

            $value = $this->attributes[$key];
        }

        $translations = $this->getTranslations($key);

        if (isset($translations[$locale]) && is_array($value)) {
            $translations[$locale] = array_merge($translations[$locale], $value);
        } else {
            $translations[$locale] = $value;
        }

        $this->attributes[$key] = $this->asJson($translations);

        return $this;
    }

    /**
     * Set multiple translations for a field.
     *
     * @param string $key
     * @param array $translations
     * @return $this
     * @throws Exception
     */
    public function setTranslations($key, array $translations)
    {
        $this->guardAgainstNonTranslatableAttribute($key);

        foreach ($translations as $locale => $translation) {
            $this->setTranslation($key, $translation, $locale);
        }

        return $this;
    }

    /**
     * Remove a translatable value for a field.
     *
     * @param string $key
     * @param string $locale
     * @return $this
     * @throws Exception
     */
    public function forgetTranslation($key, $locale)
    {
        $translations = $this->getTranslations($key);

        unset($translations[$locale]);

        parent::setAttribute($key, $translations);

        return $this;
    }

    /**
     * Remove all translatable values.
     *
     * @param string $locale
     * @return $this
     * @throws Exception
     */
    public function forgetTranslations($locale)
    {
        foreach ($this->getTranslatableAttributes() as $attribute) {
            $this->forgetTranslation($attribute, $locale);
        }

        return $this;
    }

    /**
     * Verify if the attribute is translatable.
     *
     * @param string $key
     * @return bool
     * @throws Exception
     */
    public function isTranslatableAttribute($key)
    {
        if (in_array($key, $this->getTranslatableAttributes())) {
            return true;
        }

        foreach ($this->getTranslatableAttributes() as $attribute) {
            if (Str::is("{$attribute}[*]*", $key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the model's translatable fields.
     *
     * @return array
     * @throws Exception
     */
    public function getTranslatableAttributes()
    {
        $this->initTranslationOptions();

        return $this->translationOptions->translatableFields;
    }

    /**
     * Get all translatable locales for an attribute.
     *
     * @param string $key
     * @return array
     * @throws Exception
     */
    public function getTranslatedLocales($key)
    {
        return array_keys($this->getTranslations($key));
    }

    /**
     * Determine the locale to be used.
     *
     * @param string $key
     * @param string $locale
     * @param bool $useFallbackLocale
     * @return mixed
     * @throws Exception
     */
    protected function normalizeLocale($key, $locale = null, $useFallbackLocale = true)
    {
        if (in_array($locale, $this->getTranslatedLocales($key))) {
            return $locale;
        }

        if (!$useFallbackLocale) {
            return $locale;
        }

        if (!is_null($appFallbackLocale = config('app.fallback_locale'))) {
            return $appFallbackLocale;
        }

        return $locale;
    }

    /**
     * If a field is not translatable, throw an error.
     *
     * @param string $key
     * @throws Exception
     */
    protected function guardAgainstNonTranslatableAttribute($key)
    {
        if (!$this->isTranslatableAttribute($key)) {
            throw new Exception('Attribute "' . $key . '" is not translatable!');
        }
    }

    /**
     * Instantiate the translations options.
     *
     * @return void
     * @throws Exception
     */
    protected function initTranslationOptions()
    {
        if ($this->translationOptions === null) {
            $this->translationOptions = $this->getTranslationOptions();
        }
    }
}
