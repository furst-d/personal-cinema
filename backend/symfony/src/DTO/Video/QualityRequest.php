<?php

namespace App\DTO\Video;

use App\DTO\AbstractQueryRequest;
use Symfony\Component\Validator\Constraints as Assert;

class QualityRequest extends AbstractQueryRequest
{
    #[Assert\Positive]
    public ?int $quality = null;
}
