<?php

namespace App\DTO\Storage;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class StorageRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $maxStorage;
}
