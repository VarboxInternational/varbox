<?php

namespace Varbox\Contracts;

interface TranslationServiceContract
{
    /**
     * @param bool $replace
     * @return void
     */
    public function importTranslations($replace = false);

    /**
     * @param string $file
     * @param string $path
     * @param string $locale
     * @param bool $replace
     * @return void
     */
    public function importFileTranslations($file, $path, $locale, $replace = false);

    /**
     * @param bool $replace
     * @return void
     */
    public function importJsonTranslations($replace = false);

    /**
     * @return void
     */
    public function exportTranslations();

    /**
     * @return void
     */
    public function exportFileTranslations();

    /**
     * @return void
     */
    public function exportJsonTranslations();

    /**
     * @throws /Exception
     * @return void
     */
    public function translateEmptyTranslations();
}
