<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class EmailRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[OA\Property(description: "Email address")]
    public string $email;
}
