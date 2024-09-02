<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class VideoPublicShareRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[OA\Property(description: "ID of the video to be shared")]
    public int $videoId;
}
