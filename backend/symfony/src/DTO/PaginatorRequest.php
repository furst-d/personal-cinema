<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class PaginatorRequest extends AbstractQueryRequest
{
    #[Assert\Positive]
    private int $limit;

    #[Assert\GreaterThanOrEqual(0)]
    private int $offset;

    public function __construct(int $limit = 32, int $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}
