<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class PasswordResetRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[OA\Property(description: "Password reset token")]
    public string $token;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    #[OA\Property(description: "New password")]
    public string $password;
}
