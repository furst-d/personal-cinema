<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class TokenRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[OA\Property(description: "JWT token to be validated")]
    public string $token;
}
