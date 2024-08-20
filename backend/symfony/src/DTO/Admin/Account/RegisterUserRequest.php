<?php

namespace App\DTO\Admin\Account;

use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserRequest extends UpdateUserRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $password;
}
