<?php

namespace Varbox\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Varbox\Contracts\LanguageModelContract;
use Varbox\Contracts\TranslationModelContract;
use Varbox\Contracts\TranslationServiceContract;

class TranslationService implements TranslationServiceContract
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var TranslationModelContract
     */
    protected $translationModel;

    /**
     * @var LanguageModelContract
     */
    protected $languageModel;

    /**
     * @const
     */
    const JSON_GROUP = '_json';

    /**
     * Instantiate the translation service parameters.
     *
     * @param Application $app
     * @param Filesystem $files
     * @param TranslationModelContract $translation
     * @param LanguageModelContract $language
     */
    public function __construct(Application $app, Filesystem $files, TranslationModelContract $translation, LanguageModelContract $language)
    {
        $this->app = $app;
        $this->files = $files;
        $this->translationModel = $translation;
        $this->languageModel = $language;
    }

    /**
     * Import all translation files to the database.
     *
     * @param bool $replace
     * @return void
     */
    public function importTranslations($replace = false)
    {
        foreach ($this->files->directories($this->app['path.lang']) as $path) {
            $locale = basename($path);

            foreach ($this->files->allFiles($path) as $file) {
                $this->importFileTranslations(
                    $file, $path, $locale, $replace
                );
            }
        }

        $this->importJsonTranslations($replace);
    }

    /**
     * Export all translations from every group, from database, back to it's respective lang file.
     *
     * @return void
     */
    public function exportTranslations()
    {
        $this->exportFileTranslations();

        if ($this->hasJsonTranslations()) {
            $this->exportJsonTranslations();
        }
    }

    /**
     * Import a single file's translations to the database.
     * To ignore the file from being imported, specify the $group as string.
     *
     * @param string $file
     * @param string $path
     * @param string $locale
     * @param bool $replace
     * @return void
     */
    protected function importFileTranslations($file, $path, $locale, $replace = false)
    {
        $info = pathinfo($file);
        $dir = str_replace($path . DIRECTORY_SEPARATOR, "", $info['dirname']);
        $group = $path == $dir ? $info['filename'] : $dir . '/' . $info['filename'];
        $translations = Lang::getLoader()->load($locale, $group);

        if ($translations && is_array($translations)) {
            foreach (Arr::dot($translations) as $key => $value) {
                $this->storeTranslation(
                    $key, $value, $locale, $group, $replace
                );
            }
        }
    }

    /**
     * Import all json translations to the database.
     *
     * @param bool $replace
     * @return void
     */
    protected function importJsonTranslations($replace = false)
    {
        foreach ($this->files->files($this->app['path.lang']) as $file) {
            if (strpos($file, '.json') === false) {
                continue;
            }

            $locale = basename($file, '.json');
            $translations = Lang::getLoader()->load($locale, '*', '*');

            if ($translations && is_array($translations)) {
                foreach ($translations as $key => $value) {
                    $this->storeTranslation(
                        $key, $value, $locale, self::JSON_GROUP, $replace
                    );
                }
            }
        }
    }

    /**
     * Export all translations belonging to a group, from database, back to it's respective lang file.
     * Leave the parameter $group to null, to export all files from every group.
     *
     * @return void
     */
    protected function exportFileTranslations()
    {
        $tree = $this->toTree(
            $this->translationModel
                ->withoutGroup(self::JSON_GROUP)
                ->withValue()
                ->orderBy('group')
                ->orderBy('key')
                ->get()
        );

        foreach ($tree as $locale => $groups) {
            foreach ($groups as $group => $translations) {
                $file = $this->app['path.lang'] . '/' . $locale . '/' . $group . '.php';
                $output = "<?php\n\nreturn " . var_export($translations, true) . ";\n";

                $this->files->put($file, $output);
            }
        }
    }

    /**
     * Export all json translations, from database, back to their respective json lang files.
     *
     * @return void
     */
    protected function exportJsonTranslations()
    {
        $tree = $this->toTree(
            $this->translationModel
                ->withGroup(self::JSON_GROUP)
                ->withValue()
                ->orderBy('group')
                ->orderBy('key')
                ->get(),
            true
        );

        foreach ($tree as $locale => $groups) {
            foreach ($groups as $group => $translations) {
                $path = $this->app['path.lang'] . '/' . $locale . '.json';
                $output = json_encode($translations, true);

                $this->files->put($path, $output);
            }
        }
    }

    /**
     * Import a single translation to the database.
     *
     * @param string $key
     * @param string $value
     * @param string $locale
     * @param string $group
     * @param bool $replace
     * @return void
     */
    protected function storeTranslation($key, $value, $locale, $group, $replace = false)
    {
        if (!is_string($value)) {
            return;
        }

        $translation = $this->translationModel->firstOrNew([
            'locale' => $locale,
            'group'  => $group,
            'key'    => $key,
        ]);

        if (!$translation->value || $replace === true) {
            $translation->value = $value;
        }

        $translation->save();
    }

    /**
     * Determine if the application contains imported json language files.
     *
     * @return bool
     */
    protected function hasJsonTranslations()
    {
        return $this->translationModel->whereGroup(self::JSON_GROUP)->count() > 0;
    }

    /**
     * Transform a translation collection to an in-depth array.
     *
     * @param Collection $translations
     * @param bool $json
     * @return array
     */
    protected function toTree(Collection $translations, $json = false)
    {
        $array = [];

        foreach ($translations as $translation) {
            if ($json === true) {
                $this->parseJsonSet(
                    $array[$translation->locale][$translation->group],
                    $translation->key,
                    $translation->value
                );
            } else {
                Arr::set(
                    $array[$translation->locale][$translation->group],
                    $translation->key,
                    $translation->value
                );
            }
        }

        return $array;
    }

    /**
     * Transform json translations recursively.
     *
     * @param array $array
     * @param string $key
     * @param string $value
     * @return mixed
     */
    public function parseJsonSet(&$array, $key, $value)
    {
        if (is_null($key)) {
            return $array = $value;
        }

        $array[$key] = $value;

        return $array;
    }
}
