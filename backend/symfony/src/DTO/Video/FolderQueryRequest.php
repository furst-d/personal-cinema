<?php

namespace App\DTO\Video;

use App\DTO\PaginatorRequest;
use App\Helper\DTO\SortBy;
use Symfony\Component\Validator\Constraints as Assert;

class FolderQueryRequest extends PaginatorRequest
{
    #[Assert\Positive]
    private ?int $parentId;

    #[Assert\Choice(choices: [SortBy::NAME, SortBy::UPDATE_DATE], message: "Choose a valid sort order.")]
    private ?SortBy $sortBy;

    /**
     * @param int $limit
     * @param int $offset
     * @param int|null $parentId
     * @param SortBy|null $sortBy
     */
    public function __construct(int $limit = 32, int $offset = 0, ?int $parentId = null, ?SortBy $sortBy = null)
    {
        $this->sortBy = $sortBy;
        $this->parentId = $parentId;
        parent::__construct($limit, $offset, $this->sortBy);
    }

    /**
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }
}
