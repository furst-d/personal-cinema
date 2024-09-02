<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use OpenApi\Attributes as OA;

class TokenResponse extends AbstractRequest
{
    #[OA\Property(description: "JWT access token")]
    public string $accessToken;

    #[OA\Property(description: "JWT refresh token")]
    public string $refreshToken;

    /**
     * @param string $accessToken
     * @param string $refreshToken
     */
    public function __construct(string $accessToken, string $refreshToken)
    {
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }
}
