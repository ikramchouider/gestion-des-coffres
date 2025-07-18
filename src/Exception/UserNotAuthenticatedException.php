<?php

namespace App\Exception;

use Exception;

class UserNotAuthenticatedException extends Exception
{
    public function __construct(string $message = 'User not authenticated', int $code = 401, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}