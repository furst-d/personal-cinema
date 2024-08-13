<?php

namespace App\DTO\Storage;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class StoragePaymentRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $storagePriceId;
}
