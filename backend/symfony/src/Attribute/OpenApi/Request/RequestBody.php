<?php

namespace App\Attribute\OpenApi\Request;

use Attribute;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class RequestBody extends OA\RequestBody
{
    public function __construct(string $entityClass, bool $required = true)
    {
        $content = new OA\JsonContent(
            ref: new Model(
                type: $entityClass
            ),
            type: 'object'
        );

        parent::__construct(
            required: $required,
            content: $content
        );
    }
}
