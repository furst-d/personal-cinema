<?php

namespace App\DTO\Account;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class AccountStatsResponse
{
    #[OA\Property(description: "Email of the user")]
    public string $email;

    #[Assert\NotBlank]
    #[OA\Property(description: "Used storage in GB")]
    public float $storageUsedGB;

    #[Assert\NotBlank]
    #[OA\Property(description: "Total storage limit in GB")]
    public float $storageLimitGB;

    #[Assert\NotBlank]
    #[OA\Property(description: "Number of storage upgrades")]
    public int $storageUpgradeCount;

    #[Assert\NotBlank]
    #[OA\Property(description: "Total number of videos")]
    public int $videosCount;

    #[Assert\NotBlank]
    #[OA\Property(description: "Total number of folders")]
    public int $foldersCount;

    #[Assert\NotBlank]
    #[OA\Property(description: "Total number of shared videos")]
    public int $sharedVideosCount;

    #[Assert\NotBlank]
    #[OA\Property(description: "Total number of shared folders")]
    public int $sharedFoldersCount;

    #[Assert\NotBlank]
    #[OA\Property(description: "Date of account creation")]
    public DateTimeImmutable $created;
}
