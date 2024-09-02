<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class FolderRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[OA\Property(description: "Name of the folder")]
    public string $name;

    #[Assert\Positive]
    #[OA\Property(description: "ID of the parent folder")]
    public ?int $parentId = null;
}
