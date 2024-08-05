<?php

namespace App\DTO\Video;

use App\DTO\PaginatorRequest;
use App\Helper\DTO\SortBy;
use Symfony\Component\Validator\Constraints as Assert;

class VideoQueryRequest extends PaginatorRequest
{
    #[Assert\PositiveOrZero]
    private ?int $folderId;

    #[Assert\Choice(choices: [SortBy::NAME], message: "Choose a valid sort order.")]
    private ?SortBy $sortBy;

    /**
     * @param int $limit
     * @param int $offset
     * @param int|null $folderId
     * @param SortBy|null $sortBy
     */
    public function __construct(int $limit = 32, int $offset = 0, ?int $folderId = null, ?SortBy $sortBy = null)
    {
        $this->folderId = $folderId;
        $this->sortBy = $sortBy;
        parent::__construct($limit, $offset, $this->sortBy);
    }

    /**
     * @return int|null
     */
    public function getFolderId(): ?int
    {
        return $this->folderId;
    }
}
