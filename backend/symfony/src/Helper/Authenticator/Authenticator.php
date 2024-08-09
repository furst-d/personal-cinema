<?php

namespace App\Helper\Authenticator;

use App\Exception\InternalException;
use App\Helper\Generator\RandomGenerator;
use Exception;

class Authenticator
{
    /**
     * @var RandomGenerator $generator
     */
    private RandomGenerator $generator;

    /**
     * @param RandomGenerator $generator
     */
    public function __construct(RandomGenerator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return string
     * @throws InternalException
     */
    public function generateSalt(): string
    {
        return $this->generator->generateString(64);
    }

    /**
     * @param string $password
     * @param string $salt
     * @return string
     */
    public function combinePassword(string $password, string $salt): string
    {
        return hash('sha256', $password . $salt);
    }

    /**
     * @param string $password
     * @param string $getPassword
     * @param string|null $getSalt
     * @return bool
     */
    public function verifyPassword(string $password, string $getPassword, ?string $getSalt): bool
    {
        return hash('sha256', $password . $getSalt) === $getPassword;
    }
}
