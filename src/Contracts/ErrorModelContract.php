<?php

namespace Varbox\Contracts;

interface ErrorModelContract
{
    /**
     * @param \Throwable $exception
     * @return bool
     */
    public function shouldSaveError(\Throwable $exception);

    /**
     * @param \Throwable $exception
     * @return \Varbox\Models\Error
     */
    public function saveError(\Throwable $exception);

    /**
     * @return void
     */
    public static function deleteOld();
}
