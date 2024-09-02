<?php

namespace App\DTO\Account;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class PasswordChangeRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    #[OA\Property(description: "Old password")]
    public string $oldPassword;

    #[Assert\NotBlank]
    #[Assert\Length(min: 6)]
    #[OA\Property(description: "New password")]
    public string $newPassword;
}
