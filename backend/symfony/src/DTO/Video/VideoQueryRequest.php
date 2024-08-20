<?php

namespace App\DTO\Video;

use App\DTO\PaginatorRequest;
use App\Helper\DTO\OrderBy;
use App\Helper\DTO\SortBy;
use Symfony\Component\Validator\Constraints as Assert;

class VideoQueryRequest extends PaginatorRequest
{
    #[Assert\PositiveOrZero]
    private ?int $folderId;

    #[Assert\Choice(choices: [SortBy::ID, SortBy::NAME, SortBy::UPDATE_DATE], message: "Choose a valid sort order.")]
    private SortBy $sort;

    /**
     * @param int $limit
     * @param int $offset
     * @param int|null $folderId
     * @param OrderBy $order
     * @param SortBy $sort
     */
    public function __construct(
        int $limit = 32,
        int $offset = 0,
        ?int $folderId = null,
        OrderBy $order = OrderBy::ASC,
        SortBy $sort = SortBy::ID
    )
    {
        $this->folderId = $folderId;
        $this->sort = $sort;
        parent::__construct($limit, $offset, $order, $this->sort);
    }

    /**
     * @return int|null
     */
    public function getFolderId(): ?int
    {
        return $this->folderId;
    }
}
