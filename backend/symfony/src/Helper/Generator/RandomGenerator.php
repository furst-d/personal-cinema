<?php

namespace App\Helper\Generator;

use App\Exception\InternalException;
use Exception;

class RandomGenerator
{
    /**
     * @param int $length
     * @return string
     * @throws InternalException
     */
    public function generateString(int $length): string
    {
        try {
            return bin2hex(random_bytes($length / 2));
        } catch (Exception) {
            throw new InternalException('Failed to generate random string');
        }
    }
}
