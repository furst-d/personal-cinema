<?php

namespace App\Entity\Storage;

use App\Repository\Storage\StorageUpgradePriceRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StorageUpgradePriceRepository::class)]
class StorageUpgradePrice
{
    public const STORAGE_UPGRADE_PRICE_READ = 'storageUpgradePrice:read';
    public const STORAGE_UPGRADE_PRICE_ADMIN_READ = 'storageUpgradePrice:admin:read';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups([self::STORAGE_UPGRADE_PRICE_READ, self::STORAGE_UPGRADE_PRICE_ADMIN_READ])]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups([self::STORAGE_UPGRADE_PRICE_ADMIN_READ])]
    private int $size;

    #[ORM\Column]
    #[Groups([self::STORAGE_UPGRADE_PRICE_READ, self::STORAGE_UPGRADE_PRICE_ADMIN_READ])]
    private int $priceCzk;

    #[ORM\Column]
    #[Groups([self::STORAGE_UPGRADE_PRICE_ADMIN_READ])]
    private int $percentageDiscount;

    #[ORM\Column(nullable: true)]
    #[Groups([self::STORAGE_UPGRADE_PRICE_ADMIN_READ])]
    private ?DateTimeImmutable $discountExpirationAt;

    /**
     * @param int $size
     * @param int $priceCzk
     */
    public function __construct(int $size, int $priceCzk)
    {
        $this->size = $size;
        $this->priceCzk = $priceCzk;
        $this->percentageDiscount = 0;
        $this->discountExpirationAt = null;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     * @return void
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return int
     */
    public function getPriceCzk(): int
    {
        return $this->priceCzk;
    }

    /**
     * @param int $priceCzk
     * @return void
     */
    public function setPriceCzk(int $priceCzk): void
    {
        $this->priceCzk = $priceCzk;
    }

    /**
     * @return int
     */
    public function getPercentageDiscount(): int
    {
        return $this->percentageDiscount;
    }

    /**
     * @return int
     */
    #[Groups([self::STORAGE_UPGRADE_PRICE_READ])]
    public function getActivePercentageDiscount(): int
    {
        if (!$this->discountExpirationAt) {
            return $this->percentageDiscount;
        }

        return $this->discountExpirationAt > new DateTimeImmutable() ? $this->percentageDiscount : 0;
    }

    /**
     * @param int $percentageDiscount
     * @return void
     */
    public function setPercentageDiscount(int $percentageDiscount): void
    {
        $this->percentageDiscount = $percentageDiscount;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getDiscountExpirationAt(): ?DateTimeImmutable
    {
        return $this->discountExpirationAt;
    }

    /**
     * @param DateTimeImmutable|null $discountExpirationAt
     * @return void
     */
    public function setDiscountExpirationAt(?DateTimeImmutable $discountExpirationAt): void
    {
        $this->discountExpirationAt = $discountExpirationAt;
    }

    /**
     * @return int
     */
    #[Groups([self::STORAGE_UPGRADE_PRICE_READ, self::STORAGE_UPGRADE_PRICE_ADMIN_READ])]
    public function getDiscountedPriceCzk(): int
    {
        if ($this->getActivePercentageDiscount() === 0) {
            return $this->priceCzk;
        }

        return $this->priceCzk - ($this->priceCzk * $this->percentageDiscount / 100);
    }

    /**
     * @return int
     */
    #[Groups([self::STORAGE_UPGRADE_PRICE_READ])]
    public function getSizeInGB(): int
    {
        return (int) ($this->size / 1024 / 1024 / 1024);
    }
}
