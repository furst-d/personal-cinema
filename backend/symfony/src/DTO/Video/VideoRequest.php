<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class VideoRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[OA\Property(description: "Name of the video file")]
    public string $name;

    #[Assert\Positive]
    #[OA\Property(description: "ID of the folder where the video will be uploaded")]
    public ?int $folderId = null;
}
