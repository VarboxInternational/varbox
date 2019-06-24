<?php

namespace Varbox\Exceptions;

use Exception;

class CrudException extends Exception
{
    /**
     * The exception to be thrown when a delete operation fails because the record has underlying children.
     *
     * @return static
     */
    public static function deletionRestrictedDueToChildren()
    {
        return new static('Could not delete the record because it has children!');
    }
}
