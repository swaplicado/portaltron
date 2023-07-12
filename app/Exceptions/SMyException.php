<?php

namespace App\Exceptions;

use Exception;

class SMyException extends Exception
{
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getCustomInfo()
    {
        return "Custom Info";
    }
}
