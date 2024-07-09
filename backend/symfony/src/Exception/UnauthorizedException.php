<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class UnauthorizedException extends ApiException
{
    /**
     * @param string $message
     * @param array $details
     */
    public function __construct(string $message = "Unauthorized", array $details = [])
    {
        parent::__construct($message, Response::HTTP_UNAUTHORIZED, $details);
    }
}
