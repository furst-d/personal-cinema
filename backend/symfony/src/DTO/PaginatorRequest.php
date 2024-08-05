<?php

namespace App\DTO;

use App\Helper\DTO\SortBy;
use Symfony\Component\Validator\Constraints as Assert;

class PaginatorRequest extends AbstractQueryRequest
{
    #[Assert\Positive]
    private int $limit;

    #[Assert\GreaterThanOrEqual(0)]
    private int $offset;

    private ?SortBy $orderBy;

    public function __construct(int $limit = 32, int $offset = 0, ?SortBy $orderBy = null)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->orderBy = $orderBy;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return SortBy|null
     */
    public function getOrderBy(): ?SortBy
    {
        return $this->orderBy;
    }
}
