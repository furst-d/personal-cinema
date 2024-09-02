<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class CdnNotificationRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Valid]
    #[OA\Property(description: "Video object")]
    public CdnVideoRequest $video;
}
