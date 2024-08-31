<?php

namespace App\Attribute\OpenApi\Response;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
abstract class ResponseBase extends OA\Response
{

    /**
     * @param int $responseCode
     * @param array $payloadProperties
     * @param array $requiredPayloadProperties
     * @param string $status
     * @param string $description
     */
    public function __construct(
        int $responseCode,
        array $payloadProperties = [],
        array $requiredPayloadProperties = [],
        string $status = 'success',
        string $description = ''
    ) {
        parent::__construct(
            response: $responseCode,
            description: $description ?: 'Base API response',
            content: new OA\JsonContent(
                required: ['status', 'code', 'timestamp', 'payload'],
                properties: [
                    new OA\Property(property: 'status', type: 'string', example: $status),
                    new OA\Property(property: 'code', type: 'integer', example: $responseCode),
                    new OA\Property(property: 'timestamp', type: 'integer', example: time()),
                    new OA\Property(
                        property: 'payload',
                        required: $requiredPayloadProperties,
                        properties: $payloadProperties,
                        type: 'object',
                    )
                ],
                type: 'object',
            )
        );
    }
}
