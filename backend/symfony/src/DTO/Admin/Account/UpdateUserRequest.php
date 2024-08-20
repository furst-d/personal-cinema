<?php

namespace App\DTO\Admin\Account;

use App\DTO\Account\EmailRequest;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateUserRequest extends EmailRequest
{
    #[Assert\NotNull]
    #[Assert\Type('boolean')]
    public bool $active;

    #[Assert\NotBlank]
    public array $roles;
}
