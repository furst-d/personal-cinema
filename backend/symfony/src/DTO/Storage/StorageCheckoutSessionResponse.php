<?php

namespace App\DTO\Storage;

use OpenApi\Attributes as OA;

class StorageCheckoutSessionResponse
{
    #[OA\Property(description: "Stripe checkout session id")]
    public string $checkoutSessionId;

    /**
     * @param string $checkoutSessionId
     */
    public function __construct(string $checkoutSessionId)
    {
        $this->checkoutSessionId = $checkoutSessionId;
    }
}
