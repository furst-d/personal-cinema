<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class CdnVideoRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    public string $id;

    #[Assert\NotNull]
    #[Assert\Type('boolean')]
    public bool $deleted;

    #[Assert\Type('string')]
    public ?string $title;

    #[Assert\Type('string')]
    public ?string $status;

    #[Assert\Type('string')]
    public ?string $codec = null;

    #[Assert\Type('string')]
    public ?string $extension;

    #[Assert\Type('integer')]
    public ?int $size;

    #[Assert\Type('integer')]
    public ?int $length = null;

    public ?array $conversions;

    #[Assert\Valid]
    public CdnVideoResolutionRequest $resolution;

    public array $parameters;

    #[Assert\Type('string')]
    public ?string $md5 = null;

    #[Assert\Type(DateTimeImmutable::class)]
    public DateTimeImmutable $createdAt;

    #[Assert\Type(DateTimeImmutable::class)]
    public DateTimeImmutable $updatedAt;
}
