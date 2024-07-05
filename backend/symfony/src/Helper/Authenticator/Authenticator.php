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

    /**
     * @param string $password
     * @param string $salt
     * @return string
     */
    public static function combinePassword(string $password, string $salt): string
    {
        return hash('sha256', $password . $salt);
    }

    /**
     * @param string $password
     * @param string $getPassword
     * @param string|null $getSalt
     * @return bool
     */
    public static function verifyPassword(string $password, string $getPassword, ?string $getSalt): bool
    {
        return hash('sha256', $password . $getSalt) === $getPassword;
    }
}
