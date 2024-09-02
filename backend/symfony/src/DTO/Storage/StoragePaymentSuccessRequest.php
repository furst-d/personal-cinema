<?php

namespace App\DTO\Storage;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class StoragePaymentSuccessRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[OA\Property(description: "Stripe checkout session ID")]
    public string $checkoutSessionId;
}
