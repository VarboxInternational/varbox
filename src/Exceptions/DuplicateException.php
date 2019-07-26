<?php

namespace Varbox\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

class DuplicateException extends Exception
{
    /**
     * The exception to be thrown when trying to duplicate a model that doesn't use the "HasDuplicates" trait.
     *
     * @param Model $model
     * @return static
     */
    public static function cannotBeDuplicated(Model $model)
    {
        return new static('The model "' . $model->getMorphClass() . '" cannot be duplicated because it does not use the "HasDuplicates" trait!');
    }

    /**
     * The exception to be thrown when the process of duplicating a record has failed.
     *
     * @return static
     */
    public static function duplicateFailed()
    {
        return new static('Failed duplicating the record!');
    }
}
