<?php

namespace App\DTO\Video;

use App\DTO\PaginatorRequest;
use Symfony\Component\Validator\Constraints as Assert;

class VideoQueryRequest extends PaginatorRequest
{
    #[Assert\Positive]
    public ?int $folderId = null;
}
