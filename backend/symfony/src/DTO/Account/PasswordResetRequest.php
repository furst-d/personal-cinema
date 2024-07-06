<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordResetRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    public string $token;
    #[Assert\NotBlank]

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $password;
}
