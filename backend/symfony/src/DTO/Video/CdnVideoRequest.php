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

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $title;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $status;

    #[Assert\Type('string')]
    public ?string $type = null;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    public string $extension;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    public int $size;

    #[Assert\Type('integer')]
    public ?int $length = null;

    #[Assert\Valid]
    public CdnVideoResolutionRequest $resolution;

    #[Assert\NotBlank]
    public array $parameters;

    #[Assert\Type('string')]
    public ?string $md5 = null;

    #[Assert\NotBlank]
    #[Assert\Type(DateTimeImmutable::class)]
    public DateTimeImmutable $createdAt;

    #[Assert\NotBlank]
    #[Assert\Type(DateTimeImmutable::class)]
    public DateTimeImmutable $updatedAt;
}
