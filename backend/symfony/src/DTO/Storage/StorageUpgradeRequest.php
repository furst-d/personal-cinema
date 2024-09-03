<?php

namespace App\DTO\Storage;

use App\DTO\Account\EmailRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class StorageUpgradeRequest extends EmailRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[OA\Property(description: 'Storage size in bytes')]
    public int $size;
}
