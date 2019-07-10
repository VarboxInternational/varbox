<?php

namespace Varbox\Contracts;

interface ErrorModelContract
{
    /**
     * @param \Exception $exception
     * @return bool
     */
    public function shouldSaveError(\Exception $exception);

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
