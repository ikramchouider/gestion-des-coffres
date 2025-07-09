<?php

namespace App\Service;

class SecretCodeGenerator
{
    public function generateHexCode(int $length = 36): string
    {
        $bytes = random_bytes($length / 2);
        return bin2hex($bytes);
    }
}