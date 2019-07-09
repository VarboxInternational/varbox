<?php

namespace Varbox\Contracts;

interface ErrorModelContract
{
    /**
     * @return bool
     */
    public function shouldSaveError();

    /**
     * @param \Exception $exception
     * @return void
     */
    public function saveError(\Exception $exception);

    /**
     * @return void
     */
    public static function deleteOld();
}
