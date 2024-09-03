<?php

namespace App\DTO\Admin\Account;

use App\DTO\Account\EmailRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class UpdateUserRequest extends EmailRequest
{
    #[Assert\NotNull]
    #[Assert\Type('boolean')]
    #[OA\Property(description: 'Set user active or not')]
    public bool $active;

    #[Assert\NotBlank]
    #[OA\Property(description: 'User string keyword roles')]
    public array $roles;
}
