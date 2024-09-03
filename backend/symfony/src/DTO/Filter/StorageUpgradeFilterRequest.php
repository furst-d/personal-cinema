<?php

namespace App\DTO\Filter;
use OpenApi\Attributes as OA;

class StorageUpgradeFilterRequest extends FilterRequest {
    #[OA\Property(description: 'User email')]
    public string $email;

    #[OA\Property(description: 'Stripe payment intent')]
    public string $stripePaymentIntent;

    #[OA\Property(description: 'Payment type id')]
    public int $paymentTypeId;
}
