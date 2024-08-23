<?php

namespace App\DTO\Storage;

use App\DTO\Account\EmailRequest;
use Symfony\Component\Validator\Constraints as Assert;

class StorageUpgradeRequest extends EmailRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $size;
}
