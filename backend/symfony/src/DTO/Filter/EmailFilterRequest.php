<?php

namespace App\DTO\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class EmailFilterRequest extends FilterRequest {

    #[Assert\NotBlank]
    public string $email;
}
