<?php

namespace App\Helper\Api\Exception;

use Symfony\Component\HttpFoundation\Response;

class ConflictException extends ApiException
{
    /**
     * @param string $message
     * @param array $details
     */
    public function __construct(string $message, array $details = [])
    {
        parent::__construct($message, Response::HTTP_CONFLICT, $details);
    }
}
