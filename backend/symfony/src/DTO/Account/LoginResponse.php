<?php

namespace App\DTO\Account;

use OpenApi\Attributes as OA;

class LoginResponse
{
    #[OA\Property(description: "JWT tokens")]
    public TokenResponse $tokens;

    #[OA\Property(
        ref: "#/components/schemas/Account",
        description: "Logged user",
        type: "object",
    )]
    public array $user;

    /**
     * @param TokenResponse $tokens
     * @param array $user
     */
    public function __construct(TokenResponse $tokens, array $user)
    {
        $this->tokens = $tokens;
        $this->user = $user;
    }
}
