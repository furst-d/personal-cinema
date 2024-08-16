<?php

namespace App\DTO\Video;

use App\DTO\PaginatorRequest;
use Symfony\Component\Validator\Constraints as Assert;

class SearchQueryRequest extends PaginatorRequest
{
    #[Assert\NotBlank]
    public string $phrase;
}
