<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class RegisterRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[OA\Property(description: "Registration email")]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    #[OA\Property(description: "Registration password")]
    public string $password;
}
