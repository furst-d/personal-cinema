<?php

namespace App\DTO\Filter;

class StorageUpgradeFilterRequest extends FilterRequest {
    public string $email;
    public string $stripePaymentIntent;
    public int $paymentTypeId;
}
