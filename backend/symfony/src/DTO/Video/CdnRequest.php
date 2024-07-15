<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class CdnRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Valid]
    public CdnVideoRequest $video;
}
