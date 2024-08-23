<?php

namespace App\Entity\Storage;

use App\Repository\Storage\StorageCardPaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

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
    #[Groups([StorageUpgrade::STORAGE_UPGRADE_ADMIN_READ])]
    private string $paymentIntent;

    /**
     * @param StorageUpgrade $storageUpgrade
     * @param string $paymentIntent
     */
    public function __construct(StorageUpgrade $storageUpgrade, string $paymentIntent)
    {
        $this->storageUpgrade = $storageUpgrade;
        $this->paymentIntent = $paymentIntent;
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
    public function getPaymentIntent(): string
    {
        return $this->paymentIntent;
    }
}
