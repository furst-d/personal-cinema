<?php

namespace App\DTO;

use App\Helper\DTO\OrderBy;
use App\Helper\DTO\SortBy;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class PaginatorRequest extends AbstractQueryRequest
{
    #[Assert\Positive]
    #[OA\Property(description: 'Limit of items per page')]
    private int $limit;

    #[Assert\GreaterThanOrEqual(0)]
    #[OA\Property(description: 'Offset of which item number to start from')]
    private int $offset;

    #[OA\Property(description: 'Sort by field')]
    private SortBy $sort;

    #[Assert\Choice(choices: [OrderBy::ASC, OrderBy::DESC], message: "Choose a valid order.")]
    #[OA\Property(description: 'Order by field')]
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
