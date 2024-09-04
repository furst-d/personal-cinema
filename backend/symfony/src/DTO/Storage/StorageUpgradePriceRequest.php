<?php

namespace App\DTO\Storage;

use App\DTO\AbstractRequest;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class StorageUpgradePriceRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[OA\Property(description: 'Storage size in bytes')]
    public int $size;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[OA\Property(description: 'Price in CZK')]
    public int $priceCzk;

    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    #[OA\Property(description: 'Percentage discount')]
    public int $percentageDiscount;

    #[Assert\Type(DateTimeImmutable::class)]
    #[OA\Property(description: 'Discount expiration date')]
    public ?DateTimeImmutable $discountExpirationAt = null;
}
