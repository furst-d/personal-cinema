<?php

namespace App\Entity\Storage;

use App\Repository\Storage\StorageCardPaymentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StorageCardPaymentRepository::class)]
class StorageCardPayment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'storageCardPayment', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private StorageUpgrade $storageUpgrade;

    #[ORM\Column(length: 255, unique: true)]
    private string $sessionId;

    /**
     * @param StorageUpgrade $storageUpgrade
     * @param string $sessionId
     */
    public function __construct(StorageUpgrade $storageUpgrade, string $sessionId)
    {
        $this->storageUpgrade = $storageUpgrade;
        $this->sessionId = $sessionId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return StorageUpgrade
     */
    public function getStorageUpgrade(): StorageUpgrade
    {
        return $this->storageUpgrade;
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }
}
