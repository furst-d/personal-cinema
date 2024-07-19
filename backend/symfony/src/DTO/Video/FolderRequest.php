<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class FolderRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    public string $name;

    #[Assert\Positive]
    public ?int $parentId = null;
}
