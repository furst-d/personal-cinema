<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class UploadRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    public string $name;

    #[Assert\NotBlank]
    public int $size;

    #[Assert\Positive]
    public ?int $folderId = null;
}
