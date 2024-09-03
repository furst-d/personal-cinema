<?php

namespace App\Attribute\OpenApi\Request\Query;

use App\Helper\DTO\SortBy;
use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class QuerySortBy extends OA\Parameter
{
    /**
     * @param array $choices
     * @param SortBy $default
     * @param string $description
     */
    public function __construct(array $choices, SortBy $default = SortBy::ID, string $description = 'Sort by field')
    {
        parent::__construct(
            name: 'sort',
            description: $description,
            in: 'query',
            schema: new OA\Schema(
                type: 'string',
                default: $default->value,
                enum: $choices,
                example: $default->value
            )
        );
    }
}
