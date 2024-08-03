<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordChangeRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $oldPassword;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $newPassword;
}
