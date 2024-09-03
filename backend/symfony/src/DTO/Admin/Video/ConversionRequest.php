<?php

namespace App\DTO\Admin\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class ConversionRequest extends AbstractRequest
{
    #[Assert\NotNull]
    #[Assert\Positive]
    #[OA\Property(description: 'Video width in pixels')]
    public int $width;

    #[Assert\NotNull]
    #[Assert\Positive]
    #[OA\Property(description: 'Video height in pixels')]
    public int $height;

    #[Assert\NotNull]
    #[Assert\Positive]
    #[OA\Property(description: 'Video bitrate in bps')]
    public int $bandwidth;
}
