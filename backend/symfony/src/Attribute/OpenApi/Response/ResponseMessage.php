<?php

namespace App\Attribute\OpenApi\Response;

use Attribute;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[Attribute(Attribute::TARGET_METHOD)]
class ResponseMessage extends ResponseBase
{
    public function __construct(string $message, int $responseCode = Response::HTTP_OK)
    {
        $payloadProperties = [
            new OA\Property(property: 'message', type: 'string', example: $message),
        ];

        parent::__construct(
            responseCode: $responseCode,
            payloadProperties: $payloadProperties,
            requiredPayloadProperties: ['message'],
            description: $message
        );
    }
}
