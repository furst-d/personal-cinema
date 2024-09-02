<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class NotFoundException extends ApiException
{
    /**
     * @param string $message
     * @param array $details
     */
    public function __construct(string $message = "Not found", array $details = [])
    {
        parent::__construct($message, Response::HTTP_NOT_FOUND, $details);
    }
}
