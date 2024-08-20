<?php

namespace App\DTO;

use App\Helper\DTO\OrderBy;
use App\Helper\DTO\SortBy;
use Symfony\Component\Validator\Constraints as Assert;

class PaginatorRequest extends AbstractQueryRequest
{
    #[Assert\Positive]
    private int $limit;

    #[Assert\GreaterThanOrEqual(0)]
    private int $offset;

    private SortBy $sort;

    #[Assert\Choice(choices: [OrderBy::ASC, OrderBy::DESC], message: "Choose a valid order.")]
    private OrderBy $order;

    public function __construct(int $limit = 32, int $offset = 0, OrderBy $order = OrderBy::ASC, SortBy $sort = SortBy::ID)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->order = $order;
        $this->sort = $sort;
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
     * @return SortBy
     */
    public function getSort(): SortBy
    {
        return $this->sort;
    }

    /**
     * @return OrderBy
     */
    public function getOrder(): OrderBy
    {
        return $this->order;
    }
}
