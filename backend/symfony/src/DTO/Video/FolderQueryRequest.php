<?php

namespace App\DTO\Video;

use App\DTO\PaginatorRequest;
use Symfony\Component\Validator\Constraints as Assert;

class FolderQueryRequest extends PaginatorRequest
{
    #[Assert\Positive]
    public ?int $parentId = null;
}
