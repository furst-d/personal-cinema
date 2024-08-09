<?php

namespace App\DTO\Video;

use App\DTO\Account\EmailRequest;
use Symfony\Component\Validator\Constraints as Assert;

class VideoShareRequest extends EmailRequest
{
    #[Assert\Positive]
    public int $videoId;
}
