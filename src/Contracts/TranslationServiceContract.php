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
     * @return void
     */
    public function exportTranslations();

    /**
     * @throws /Exception
     * @return void
     */
    public function autoTranslate();
}
