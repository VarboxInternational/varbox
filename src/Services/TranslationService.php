<?php

namespace Varbox\Services;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\Finder\Finder;
use Varbox\Contracts\LanguageModelContract;
use Varbox\Contracts\TranslationModelContract;

class TranslationService
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
     * @var Dispatcher
     */
    protected $events;

    /**
     * @var Collection
     */
    protected $languages;

    /**
     * All translatable functions Laravel provides.
     *
     * @var array
     */
    protected static $functions = [
        'trans',
        'trans_choice',
        'Lang::get',
        'Lang::choice',
        'Lang::trans',
        'Lang::transChoice',
        '@lang',
        '@choice',
        '__',
    ];

    /**
     * @const
     */
    const JSON_GROUP = '_json';

    /**
     * Instantiate the translation service parameters.
     *
     * @param Application $app
     * @param Filesystem $files
     * @param Dispatcher $events
     */
    public function __construct(Application $app, Filesystem $files, Dispatcher $events, LanguageModelContract $language)
    {
        $this->app = $app;
        $this->files = $files;
        $this->events = $events;
        $this->languages = $language->onlyActive()->get();
    }

    /**
     * Create a database record for a missing translation key.
     *
     * @param string $group
     * @param string $key
     */
    public function createMissingTranslation($group, $key)
    {
        app(TranslationModelContract::class)->firstOrCreate(array(
            'locale' => $this->app['config']['app.locale'],
            'group' => $group,
            'key' => $key,
        ));
    }

    /**
     * Find usages of Laravel file translations methods everywhere in the application.
     *
     * @param string|null $path
     * @return int
     */
    public function findMissingTranslations($path = null)
    {
        $path = $path ?: base_path();
        $groupKeys = $stringKeys = [];
        $groupPattern = $this->getGroupPattern();
        $stringPattern = $this->getStringPattern();

        $finder = new Finder();
        $finder->in($path)->exclude([
            'bootstrap',
            'config',
            'database',
            'public',
            'storage',
            'tests',
            'resources/assets',
            'resources/stubs',
            'resources/lang/vendor',
            'vendor',
            'node_modules',
        ])->name('*.php')->name('*.twig');

        foreach ($finder->files() as $file) {
            if (preg_match_all("/$groupPattern/siU", $file->getContents(), $matches)) {
                foreach ($matches[2] as $key) {
                    $groupKeys[] = $key;
                }
            }

            if (preg_match_all("/$stringPattern/siU", $file->getContents(), $matches)) {
                foreach ($matches['string'] as $key) {
                    if (preg_match("/(^[a-zA-Z0-9_-]+([.][^\1)\ ]+)+$)/siU", $key, $groupMatches)) {
                        continue;
                    }

                    $stringKeys[] = $key;
                }
            }
        }

        $groupKeys = array_unique($groupKeys);
        $stringKeys = array_unique($stringKeys);

        foreach($groupKeys as $key) {
            list($group, $item) = explode('.', $key, 2);

            $this->createMissingTranslation($group, $item);
        }

        foreach($stringKeys as $key){
            $group = self::JSON_GROUP;

            $this->createMissingTranslation($group, $key);
        }

        return count($groupKeys + $stringKeys);
    }

    /**
     * Import all translation files to the database.
     *
     * @param bool $replace
     * @return int
     * @throws TranslationException
     */
    public function importAllTranslations($replace = false)
    {
        try {
            $count = 0;

            foreach ($this->files->directories($this->app['path.lang']) as $path) {
                $locale = basename($path);

                if (!$this->translationShouldBeImported($locale)) {
                    continue;
                }

                foreach ($this->files->allfiles($path) as $file) {
                    $count += $this->importFileTranslations(
                        null, $file, $path, $locale, $replace
                    );
                }
            }

            $count += $this->importJsonTranslations($replace);

            return $count;
        } catch (Exception $e) {
            throw TranslationException::importTranslationFailed();
        }
    }

    /**
     * Import all translation files belonging to a group to the database.
     *
     * @param string $group
     * @param bool $replace
     * @return int
     * @throws TranslationException
     */
    public function importGroupTranslations($group, $replace = false)
    {
        try {
            $count = 0;

            foreach ($this->files->directories($this->app['path.lang']) as $path) {
                $locale = basename($path);

                if (!$this->translationShouldBeImported($locale)) {
                    continue;
                }

                foreach ($this->files->allfiles($path) as $file) {
                    $count += $this->importFileTranslations(
                        $group, $file, $path, $locale, $replace
                    );
                }
            }

            return $count;
        } catch (Exception $e) {
            throw TranslationException::importTranslationFailed();
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
     * @return bool
     * @throws TranslationException
     */
    public function importTranslation($key, $value, $locale, $group, $replace = false)
    {
        if (is_array($value)) {
            return false;
        }

        try {
            $translation = app(TranslationModelContract::class)->firstOrNew([
                'locale' => $locale,
                'group'  => $group,
                'key'    => $key,
            ]);

            if (!$translation->value || $replace === true) {
                $translation->value = (string)$value;
            }

            $translation->save();

            return true;
        } catch (Exception $e) {
            throw TranslationException::importTranslationFailed();
        }
    }

    /**
     * Export all translations from every group, from database, back to it's respective lang file.
     *
     * @return int
     */
    public function exportAllTranslations()
    {
        $count = 0;
        $groups = app(TranslationModelContract::class)->distinctGroup()->whereNotNull('value')->get('group');

        foreach ($groups as $group) {
            if ($group == self::JSON_GROUP) {
                $count += $this->exportJsonTranslations();
            } else {
                $count += $this->exportGroupFilesTranslations($group->group);
            }
        }

        return $count;
    }

    /**
     * Export all translations belonging to a group, from database, back to it's respective lang file.
     *
     * @param string $group
     * @return int
     */
    public function exportGroupTranslations($group)
    {
        return $this->exportGroupFilesTranslations($group);
    }

    /**
     * Determine if a translation should be imported.
     *
     * A translation should be imported if the language from database with the corresponding locale.
     * Has the "active" column set to "yes" (1).
     *
     * @param string $locale
     * @return bool
     */
    protected function translationShouldBeImported($locale)
    {
        return in_array($locale, $this->languages->pluck('code')->toArray());
    }

    /**
     * Import a single file's translations to the database.
     * To ignore the file from being imported, specify the $group as string.
     *
     * @param string|null $group
     * @param string $file
     * @param string $path
     * @param string $locale
     * @param bool $replace
     * @return int
     * @throws TranslationException
     */
    protected function importFileTranslations($group = null, $file, $path, $locale, $replace = false)
    {
        $count = 0;
        $info = pathinfo($file);

        if (!is_null($group) && $group != $info['filename']) {
            return $count;
        }

        $dir = str_replace($path . DIRECTORY_SEPARATOR, "", $info['dirname']);
        $group = $path == $dir ? $info['filename'] : $dir . '/' . $info['filename'];
        $translations = Lang::getLoader()->load($locale, $group);

        if ($translations && is_array($translations)) {
            foreach (array_dot($translations) as $key => $value) {
                $count += $this->importTranslation(
                    $key, $value, $locale, $group, $replace
                );
            }
        }

        return $count;
    }

    /**
     * Import all json translations to the database.
     *
     * @param bool $replace
     * @return int
     * @throws TranslationException
     */
    protected function importJsonTranslations($replace = false)
    {
        $count = 0;

        foreach ($this->files->files($this->app['path.lang']) as $file) {
            if (strpos($file, '.json') === false) {
                continue;
            }

            $locale = basename($file, '.json');
            $translations = Lang::getLoader()->load($locale, '*', '*');

            if ($translations && is_array($translations)) {
                foreach ($translations as $key => $value) {
                    $count += $this->importTranslation(
                        $key, $value, $locale, self::JSON_GROUP, $replace
                    );
                }
            }
        }

        return $count;
    }

    /**
     * Export all translations belonging to a group, from database, back to it's respective lang file.
     * Leave the parameter $group to null, to export all files from every group.
     *
     * @param string|null $group
     * @return int
     */
    protected function exportGroupFilesTranslations($group = null)
    {
        if (is_null($group)) {
            return $this->exportAllTranslations();
        }

        $tree = $this->toTree(
            app(TranslationModelContract::class)->ofTranslatedGroup($group)->orderByGroupKeys()->get()
        );

        foreach ($tree as $locale => $groups) {
            if (isset($groups[$group])) {
                $file = $this->app['path.lang'] . '/' . $locale . '/' . $group . '.php';
                $translations = $groups[$group];
                $output = "<?php\n\nreturn " . var_export($translations, true) . ";\n";

                $this->files->put($file, $output);
            }
        }

        return app(TranslationModelContract::class)->ofTranslatedGroup($group)->count();
    }

    /**
     * Export all json translations, from database, back to their respective json lang files.
     *
     * @return int
     */
    protected function exportJsonTranslations()
    {
        $tree = $this->toTree(
            app(TranslationModelContract::class)->ofTranslatedGroup(self::JSON_GROUP)->orderByGroupKeys()->get(), true
        );

        foreach ($tree as $locale => $groups) {
            if (isset($groups[self::JSON_GROUP])) {
                $path = $this->app['path.lang'] . '/' . $locale . '.json';
                $translations = $groups[self::JSON_GROUP];
                $output = json_encode($translations, true);

                $this->files->put($path, $output);
            }
        }

        return app(TranslationModelContract::class)->ofTranslatedGroup(self::JSON_GROUP)->count();
    }

    /**
     * Get the translation pattern applicable to a translation group.
     *
     * Matching pattern conditions in order:
     * - must not have an alphanum or _ or > before real method
     * - must start with one of the functions
     * - match opening parenthesis
     * - match " or '
     * - start a new group to match
     * - must start with group
     * - be followed by one or more items/keys
     * - close group
     * - close quote
     * - close parentheses or new parameter
     *
     * @return string
     */
    protected function getGroupPattern()
    {
        return
            "[^\w|>]" .
            "(" . implode('|', self::$functions) . ")" .
            "\(" .
            "[\'\"]" .
            "(" .
            "[a-zA-Z0-9_-]+" .
            "([.|\/][^\1)]+)+" .
            ")" .
            "[\'\"]" .
            "[\),]";
    }

    /**
     * Get the translation pattern applicable to a translation string.
     *
     * Matching pattern conditions in order:
     * - must not have an alphanum or _ or > before real method
     * - must start with one of the functions
     * - match opening parenthesis
     * - match " or ' and store in {quote}
     * - match any string that can be {quote} escaped
     * - match " or ' previously matched
     *
     * @return string
     */
    protected function getStringPattern()
    {
        return
            "[^\w|>]" .
            "(" . implode('|', self::$functions) . ")" .
            "\(" .
            "(?P<quote>['\"])" .
            "(?P<string>(?:\\\k{quote}|(?!\k{quote}).)*)" .
            "\k{quote}" .
            "[\),]";
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
            $json === true ?
                $this->parseJsonSet($array[$translation->locale][$translation->group], $translation->key, $translation->value) :
                array_set($array[$translation->locale][$translation->group], $translation->key, $translation->value);
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
