<?php

namespace App\DTO\Storage;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class StorageInfoResponse
{
    #[OA\Property(description: "Total storage in bytes")]
    public int $totalStorage;

    #[Assert\NotBlank]
    #[OA\Property(description: "Used storage in bytes")]
    public int $usedStorage;

    /**
     * @param int $totalStorage
     * @param int $usedStorage
     */
    public function __construct(int $totalStorage, int $usedStorage)
    {
        $this->totalStorage = $totalStorage;
        $this->usedStorage = $usedStorage;
    }
}
