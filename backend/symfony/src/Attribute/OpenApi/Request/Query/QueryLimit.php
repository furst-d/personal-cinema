<?php

namespace App\Attribute\OpenApi\Request\Query;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class QueryLimit extends OA\Parameter
{

    /**
     * @param int $default
     * @param string $description
     */
    public function __construct(int $default = 32, string $description = 'Limit of items per page')
    {
        parent::__construct(
            name: 'limit',
            description: $description,
            in: 'query',
            required: false,
            schema: new OA\Schema(
                type: 'integer',
                default: $default,
                example: $default
            )
        );
    }
}
