<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class TooLargeException extends ApiException
{
    /**
     * @param string $message
     * @param array $details
     */
    public function __construct(string $message, array $details = [])
    {
        parent::__construct($message, Response::HTTP_REQUEST_ENTITY_TOO_LARGE, $details);
    }
}
