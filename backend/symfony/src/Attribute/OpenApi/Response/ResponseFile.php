<?php

namespace App\Attribute\OpenApi\Response;

use App\Helper\File\MimeType;
use Attribute;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class ResponseFile extends OA\Response
{
    public function __construct(MimeType $mimeType, string $description = 'File Response', int $responseCode = Response::HTTP_OK)
    {
        parent::__construct(
            response: $responseCode,
            description: $description,
            content: new OA\MediaType(
                mediaType: $mimeType->value,
                schema: new OA\Schema(
                    type: 'string',
                    format: 'binary'
                )
            )
        );
    }
}
