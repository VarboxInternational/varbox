<?php

namespace Varbox\Exceptions;

use Exception;

class EmailException extends Exception
{
    /**
     * @param string $type
     * @return static
     */
    public static function emailNotFound($type)
    {
        return new static('No email with the "' . $type . '" type was found!', 404);
    }

    /**
     * @return static
     */
    public static function viewNotFound()
    {
        return new static('Email view not found!', 404);
    }
}
