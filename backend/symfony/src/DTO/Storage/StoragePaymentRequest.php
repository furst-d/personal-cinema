<?php

namespace App\DTO\Storage;

use App\DTO\AbstractQueryRequest;
use Symfony\Component\Validator\Constraints as Assert;

class StoragePaymentRequest extends AbstractQueryRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $storagePriceId;
}
