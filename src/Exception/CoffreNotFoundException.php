<?php

namespace App\Exception;

use RuntimeException;

class CoffreNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Coffre not found', int $code = 404, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}