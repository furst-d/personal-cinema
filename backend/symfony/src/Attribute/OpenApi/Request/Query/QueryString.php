<?php

namespace App\Attribute\OpenApi\Request\Query;

use Attribute;
use OpenApi\Attributes as OA;

#[Attribute(Attribute::TARGET_METHOD)]
class QueryString extends OA\Parameter
{

    /**
     * @param string $name
     * @param string $description
     * @param bool $required
     * @param string|null $default
     */
    public function __construct(string $name, string $description, bool $required = false, ?string $default = null)
    {
        $schema = new OA\Schema(
            type: 'string'
        );

        if ($default !== null) {
            $schema->default = $default;
            $schema->example = $default;
        }

        parent::__construct(
            name: $name,
            description: $description,
            in: 'query',
            required: $required,
            schema: $schema
        );
    }
}
