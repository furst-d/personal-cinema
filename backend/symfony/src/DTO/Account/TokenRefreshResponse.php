<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use OpenApi\Attributes as OA;

class TokenRefreshResponse extends AbstractRequest
{
    #[OA\Property(description: "JWT token")]
    public AccessTokenResponse $tokens;

    /**
     * @param AccessTokenResponse $tokens
     */
    public function __construct(AccessTokenResponse $tokens)
    {
        $this->tokens = $tokens;
    }
}
