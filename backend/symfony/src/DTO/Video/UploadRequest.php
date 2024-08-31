<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class UploadRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[OA\Property(description: "Name of the video file")]
    public string $name;

    #[Assert\NotBlank]
    #[OA\Property(description: "Size of the video file in bytes")]
    public int $size;

    #[Assert\Positive]
    #[OA\Property(description: "ID of the folder where the video will be uploaded")]
    public ?int $folderId = null;
}
