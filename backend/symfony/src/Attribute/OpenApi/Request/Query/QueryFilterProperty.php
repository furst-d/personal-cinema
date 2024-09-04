<?php

namespace App\Attribute\OpenApi\Request\Query;

use OpenApi\Attributes as OA;

class QueryFilterProperty
{
    /**
     * @var string $name
     */
    private string $name;

    /**
     * @var string $description
     */
    private string $description;

    /**
     * @var string $type
     */
    private string $type;

    /**
     * @var array|null $enum
     */
    private ?array $enum;

    /**
     * @var mixed $default
     */
    private mixed $default;

    /**
     * @var mixed $example
     */
    private mixed $example;

    /**
     * @var OA\Items|null $items
     */
    private ?OA\Items $items;

    /**
     * @param string $name
     * @param string $description
     * @param string $type
     * @param array|null $enum
     * @param mixed|null $default
     * @param mixed|null $example
     * @param OA\Items|null $items
     */
    public function __construct(
        string $name,
        string $description,
        string $type = "string",
        ?array $enum = null,
        mixed $default = null,
        mixed $example = null,
        ?OA\Items $items = null
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->enum = $enum;
        $this->default = $default;
        $this->example = $example;
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Convert to OpenApi Property
     *
     * @return OA\Property
     */
    public function toOpenApiProperty(): OA\Property
    {
        return new OA\Property(
            property: $this->name,
            description: $this->description,
            type: $this->type,
            items: $this->items,
            default: $this->default,
            enum: $this->enum,
            example: $this->example,
        );
    }
}

