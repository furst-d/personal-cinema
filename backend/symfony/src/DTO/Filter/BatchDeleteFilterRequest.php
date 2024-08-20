<?php

namespace App\DTO\Filter;

use Symfony\Component\Validator\Constraints as Assert;

class BatchDeleteFilterRequest extends FilterRequest {
    #[Assert\NotBlank]
    #[Assert\All([
        new Assert\Type('integer'),
        new Assert\Positive,
    ])]
    public array $ids;
}
