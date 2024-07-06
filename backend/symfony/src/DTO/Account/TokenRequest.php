<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class TokenRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    public string $token;
}
