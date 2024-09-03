<?php

namespace App\DTO\Filter;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class BatchDeleteFilterRequest extends FilterRequest {
    #[Assert\NotBlank]
    #[Assert\All([
        new Assert\Type('integer'),
        new Assert\Positive,
    ])]
    #[OA\Property(description: 'List of integer ids to delete')]
    public array $ids;
}
