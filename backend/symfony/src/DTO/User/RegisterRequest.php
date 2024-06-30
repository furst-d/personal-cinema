<?php

namespace App\DTO\User;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $password;
}
