<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteAccountRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    public string $password;
}
