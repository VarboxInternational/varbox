<?php

namespace Varbox\Contracts;

interface RedirectModelContract
{
    /**
     * @param string $value
     */
    public function setOldUrlAttribute($value);

    /**
     * @param string $value
     */
    public function setNewUrlAttribute($value);

    /**
     * @param RedirectModelContract $model
     * @param string $finalUrl
     * @return void
     */
    public function syncOldRedirects(self $model, $finalUrl);

    /**
     * @param string $path
     * @return RedirectModelContract|null
     */
    public static function findValidOrNull($path);

    /**
     * @return void
     */
    public static function exportToFile();
}
