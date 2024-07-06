<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class EmailRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email;
}
