<?php

namespace App\DTO\User;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class LoginRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    public string $password;
}
