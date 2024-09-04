<?php

namespace App\DTO\Filter;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class EmailFilterRequest extends FilterRequest {

    #[Assert\NotBlank]
    #[OA\Property(description: 'Email')]
    public string $email;
}
