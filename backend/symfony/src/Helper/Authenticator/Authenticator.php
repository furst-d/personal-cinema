<?php

namespace App\Helper\Authenticator;

use Random\RandomException;

class Authenticator
{
    /**
     * @return string
     * @throws RandomException
     */
    public static function generateSalt(): string
    {
        return bin2hex(random_bytes(32));
    }

    public static function combinePassword(string $password, string $salt): string
    {
        return hash('sha256', $password . $salt);
    }
}
