<?php

namespace App\Exception;

use RuntimeException;

class AlreadyLoggedInException extends RuntimeException
{
    public function __construct(string $message = 'User is already logged in', int $code = 400, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}