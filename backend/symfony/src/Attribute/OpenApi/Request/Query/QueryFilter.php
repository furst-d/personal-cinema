<?php

namespace App\Attribute\OpenApi\Request\Query;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class QueryFilter extends OA\Parameter
{
    /**
     * @param QueryFilterProperty[] $properties
     * @param string $description
     * @param bool $required
     */
    public function __construct(array $properties, string $description = "Filter criteria in JSON format", bool $required = false)
    {
        $schemaProperties = [];

        foreach ($properties as $property) {
            $schemaProperties[$property->getName()] = $property->toOpenApiProperty();
        }

        parent::__construct(
            name: 'filter',
            description: $description,
            in: 'query',
            required: $required,
            schema: new OA\Schema(
                properties: $schemaProperties,
                type: 'object'
            )
        );
    }
}
