<?php

namespace App\DTO\Video;

use App\DTO\Account\EmailRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class VideoShareRequest extends EmailRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[OA\Property(description: "ID of the video to be shared")]
    public int $videoId;
}
