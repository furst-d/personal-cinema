<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class CdnVideoResolutionRequest extends AbstractRequest
{
    #[Assert\Type('integer')]
    public ?int $width = null;

    #[Assert\Type('integer')]
    public ?int $height = null;
}
