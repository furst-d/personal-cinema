<?php

namespace App\DTO\Storage;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class StoragePaymentSuccessRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    public string $checkoutSessionId;
}
