<?php

namespace App\DTO\Video;

use App\DTO\PaginatorRequest;
use App\Helper\DTO\OrderBy;
use App\Helper\DTO\SortBy;
use Symfony\Component\Validator\Constraints as Assert;

class FolderQueryRequest extends PaginatorRequest
{
    #[Assert\PositiveOrZero]
    private ?int $parentId;

    #[Assert\Choice(choices: [SortBy::NAME, SortBy::UPDATE_DATE], message: "Choose a valid sort order.")]
    private SortBy $sort;

    /**
     * @param int $limit
     * @param int $offset
     * @param int|null $parentId
     * @param OrderBy $order
     * @param SortBy $sort
     */
    public function __construct(
        int $limit = 32,
        int $offset = 0,
        ?int $parentId = null,
        OrderBy $order = OrderBy::ASC,
        SortBy $sort = SortBy::NAME
    )
    {
        $this->sort = $sort;
        $this->parentId = $parentId;
        parent::__construct($limit, $offset, $order, $this->sort);
    }

    /**
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }
}
