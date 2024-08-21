<?php

namespace App\DTO\Admin\Video;

use App\DTO\PaginatorRequest;
use App\Helper\DTO\OrderBy;
use App\Helper\DTO\SortBy;
use Symfony\Component\Validator\Constraints as Assert;

class VideoConversionQueryRequest extends PaginatorRequest
{
    #[Assert\Choice(choices: [SortBy::ID, SortBy::WIDTH, SortBy::HEIGHT, SortBy::BANDWIDTH], message: "Choose a valid sort order.")]
    private SortBy $sort;

    /**
     * @param int $limit
     * @param int $offset
     * @param OrderBy $order
     * @param SortBy $sort
     */
    public function __construct(
        int $limit = 32,
        int $offset = 0,
        OrderBy $order = OrderBy::ASC,
        SortBy $sort = SortBy::ID
    )
    {
        $this->sort = $sort;
        parent::__construct($limit, $offset, $order, $this->sort);
    }
}