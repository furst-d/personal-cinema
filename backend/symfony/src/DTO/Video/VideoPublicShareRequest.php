<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class VideoPublicShareRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $videoId;
}
