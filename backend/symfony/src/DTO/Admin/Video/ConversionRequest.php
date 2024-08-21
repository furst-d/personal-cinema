<?php

namespace App\DTO\Admin\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class ConversionRequest extends AbstractRequest
{
    #[Assert\NotNull]
    #[Assert\Positive]
    public int $width;

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $height;

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $bandwidth;
}
