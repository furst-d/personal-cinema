<?php

namespace App\DTO\Storage;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class StorageRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[OA\Property(description: 'Maximum storage size in bytes')]
    public int $maxStorage;
}
