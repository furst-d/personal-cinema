<?php

namespace App\DTO\Storage;

use App\DTO\AbstractRequest;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class StorageUpgradePriceRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $size;

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $priceCzk;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    public int $percentageDiscount;

    #[Assert\Type(DateTimeImmutable::class)]
    public ?DateTimeImmutable $discountExpirationAt = null;
}
