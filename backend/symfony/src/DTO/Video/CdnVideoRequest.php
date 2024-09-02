<?php

namespace App\DTO\Video;

use App\DTO\AbstractRequest;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class CdnVideoRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[Assert\Uuid]
    #[OA\Property(description: "ID of the video")]
    public string $id;

    #[Assert\NotNull]
    #[Assert\Type('boolean')]
    #[OA\Property(description: "Is video deleted")]
    public bool $deleted;

    #[Assert\Type('string')]
    #[OA\Property(description: "Title of the video")]
    public ?string $title;

    #[Assert\Type('string')]
    #[OA\Property(description: "Status of the video")]
    public ?string $status;

    #[Assert\Type('string')]
    #[OA\Property(description: "Used codec of the video")]
    public ?string $codec = null;

    #[Assert\Type('string')]
    #[OA\Property(description: "Type of the video")]
    public ?string $extension;

    #[Assert\Type('integer')]
    #[OA\Property(description: "Size of the video in bytes")]
    public ?int $size;

    #[Assert\Type('integer')]
    #[OA\Property(description: "Length of the video in seconds")]
    public ?int $length = null;

    #[OA\Property(description: "Generated conversions")]
    public ?array $conversions;

    #[Assert\Valid]
    #[OA\Property(description: "Resolution of the video")]
    public CdnVideoResolutionRequest $resolution;

    #[OA\Property(description: "Custom parameters defined by backend containing additional information about the video")]
    public array $parameters;

    #[Assert\Type('string')]
    #[OA\Property(description: "MD5 hash of the video")]
    public ?string $md5 = null;

    #[Assert\Type(DateTimeImmutable::class)]
    #[OA\Property(description: "Date and time when the video was created")]
    public DateTimeImmutable $createdAt;

    #[Assert\Type(DateTimeImmutable::class)]
    #[OA\Property(description: "Date and time when the video was last updated")]
    public DateTimeImmutable $updatedAt;
}
