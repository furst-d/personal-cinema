<?php

namespace App\Attribute\OpenApi\Request\Query;

use App\Helper\DTO\OrderBy;
use App\Helper\DTO\SortBy;
use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class QueryOrderBy extends OA\Parameter
{
    /**
     * @param OrderBy $default
     * @param string $description
     */
    public function __construct(OrderBy $default = OrderBy::ASC, string $description = 'Order by field')
    {
        parent::__construct(
            name: 'order',
            description: $description,
            in: 'query',
            schema: new OA\Schema(
                type: 'string',
                default: $default->value,
                enum: [OrderBy::ASC, OrderBy::DESC],
                example: $default->value,
            )
        );
    }
}
