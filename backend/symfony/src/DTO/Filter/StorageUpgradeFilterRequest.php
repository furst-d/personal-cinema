<?php

namespace App\DTO\Filter;

class StorageUpgradeFilterRequest extends FilterRequest {
    public string $email;
    public string $stripeSessionId;
    public int $paymentTypeId;
}
