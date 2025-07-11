<?php

namespace App\Exception;

use RuntimeException;

class MissingCredentialsException extends RuntimeException
{
    public function __construct(string $message = 'Required credentials are missing', int $code = 400, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}