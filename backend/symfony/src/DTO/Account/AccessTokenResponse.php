<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use OpenApi\Attributes as OA;

class AccessTokenResponse extends AbstractRequest
{
    #[OA\Property(description: "JWT access token")]
    public string $accessToken;

    /**
     * @param string $accessToken
     */
    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }
}
