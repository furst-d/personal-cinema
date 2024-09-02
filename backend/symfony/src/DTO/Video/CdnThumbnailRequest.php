<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class CdnThumbnailRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Valid]
    #[OA\Property(description: "Video for which thumbnails are generated")]
    public CdnVideoRequest $video;

    #[Assert\NotBlank]
    #[OA\Property(description: "Thumbnail links generated for the video")]
    public array $thumbs;
}
