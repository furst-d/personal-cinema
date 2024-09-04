<?php

namespace App\Attribute\OpenApi\Request\Query;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class QueryOffset extends OA\Parameter
{
    /**
     * @param int $default
     * @param string $description
     */
    public function __construct(int $default = 0, string $description = 'Offset of which item number to start from')
    {
        parent::__construct(
            name: 'offset',
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
