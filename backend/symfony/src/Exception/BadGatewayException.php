<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class BadGatewayException extends ApiException
{
    /**
     * @param string $message
     * @param array $details
     */
    public function __construct(string $message, array $details = [])
    {
        parent::__construct($message, Response::HTTP_BAD_GATEWAY, $details);
    }
}
