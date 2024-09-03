<?php

namespace App\DTO\Admin\Account;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class RegisterUserRequest extends UpdateUserRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    #[OA\Property(description: 'User password')]
    public string $password;
}
