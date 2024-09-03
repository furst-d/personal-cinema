<?php

namespace App\Attribute\OpenApi\Request\Query;

use OpenApi\Attributes as OA;

class QueryFilterPropertyIds extends QueryFilterProperty
{
    public function __construct()
    {
        parent::__construct(
            name: 'ids',
            description: 'List of integer IDs to filter',
            type: 'array',
            default: [],
            example: [1, 2, 3],
            items: new OA\Items(type: 'integer')
        );
    }
}
