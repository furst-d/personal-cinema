<?php

namespace App\Attribute\OpenApi\Request\Query;

class QueryFilterPropertyEmail extends QueryFilterProperty
{
    public function __construct()
    {
        parent::__construct(
            name: 'email',
            description: 'User email',
            type: 'string',
            example: 'user@example.com'
        );
    }
}
