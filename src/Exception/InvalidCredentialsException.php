<?php

namespace App\Exception;

use RuntimeException;

class InvalidCredentialsException extends RuntimeException
{
    public function __construct(string $message = 'Invalid email or password', int $code = 401, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}