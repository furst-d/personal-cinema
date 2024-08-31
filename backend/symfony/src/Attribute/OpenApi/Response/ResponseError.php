<?php

namespace App\Attribute\OpenApi\Response;

use App\Exception\ApiException;
use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ResponseError extends ResponseBase
{
    public function __construct(ApiException $exception)
    {
        $payloadProperties = [
            new OA\Property(property: 'message', type: 'string', example: $exception->getMessage()),
        ];

        $requiredPayloadProperties = ['message'];

        if ($exception->getTag()) {
            $payloadProperties[] = new OA\Property(property: 'tag', type: 'string', example: $exception->getTag());
        }

        if (!empty($exception->getDetails())) {
            $details = [];
            foreach ($exception->getDetails() as $key => $value) {
                $details[$key] = new OA\Property(
                    property: $key,
                    type: 'string',
                    example: $value
                );
            }

            $payloadProperties[] = new OA\Property(property: 'details', properties: $details, type: 'object');
        }

        parent::__construct(
            responseCode: $exception->getCode(),
            payloadProperties: $payloadProperties,
            requiredPayloadProperties: $requiredPayloadProperties,
            status: 'error',
            description: $exception->getMessage()
        );
    }
}
