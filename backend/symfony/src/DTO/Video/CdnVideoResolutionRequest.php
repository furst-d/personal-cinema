<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class CdnVideoResolutionRequest extends AbstractRequest
{
    #[Assert\Type('integer')]
    #[OA\Property(description: "Width of the video")]
    public ?int $width = null;

    #[Assert\Type('integer')]
    #[OA\Property(description: "Height of the video")]
    public ?int $height = null;
}
