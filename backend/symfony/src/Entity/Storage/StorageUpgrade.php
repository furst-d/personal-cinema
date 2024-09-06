<?php

namespace App\Entity\Storage;

use App\Entity\Account\Account;
use App\Helper\Storage\ByteSizeConverter;
use App\Helper\Storage\StoragePaymentInfo;
use App\Helper\Storage\StoragePaymentType;
use App\Repository\Storage\StorageUpgradeRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StorageUpgradeRepository::class)]
class StorageUpgrade
{
    public const STORAGE_UPGRADE_READ = 'storage_upgrade:read';
    public const STORAGE_UPGRADE_ADMIN_READ = 'storage_upgrade:admin:read';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::STORAGE_UPGRADE_ADMIN_READ])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'storageUpgrades')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::STORAGE_UPGRADE_ADMIN_READ])]
    private Account $account;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups([self::STORAGE_UPGRADE_ADMIN_READ])]
    private int $paymentType;

    #[ORM\Column(type: Types::INTEGER)]
    #[Groups([self::STORAGE_UPGRADE_READ, self::STORAGE_UPGRADE_ADMIN_READ])]
    private int $priceCzk;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups([self::STORAGE_UPGRADE_ADMIN_READ])]
    private int $size;

    #[ORM\Column]
    #[Groups([self::STORAGE_UPGRADE_READ, self::STORAGE_UPGRADE_ADMIN_READ])]
    private DateTimeImmutable $createdAt;

    #[ORM\OneToOne(mappedBy: 'storageUpgrade', cascade: ['persist', 'remove'])]
    #[Groups([self::STORAGE_UPGRADE_ADMIN_READ])]
    private ?StorageCardPayment $storageCardPayment = null;

    /**
     * @param Account $account
     * @param int $priceCzk
     * @param int $size
     * @param StoragePaymentType $paymentType
     * @param string|null $cardPaymentIntent
     */
    public function __construct(
        Account $account,
        int $priceCzk,
        int $size,
        StoragePaymentType $paymentType,
        ?string $cardPaymentIntent = null
    )
    {
        $this->account = $account;
        $this->paymentType = $paymentType->value;
        $this->priceCzk = $priceCzk;
        $this->size = $size;
        $this->createdAt = new DateTimeImmutable();

        if ($cardPaymentIntent) {
            $this->storageCardPayment = new StorageCardPayment($this, $cardPaymentIntent);
        }
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @return StoragePaymentType
     */
    public function getPaymentType(): StoragePaymentType
    {
        return StoragePaymentType::from($this->paymentType);
    }

    /**
     * @return StoragePaymentInfo
     */
    #[Groups([self::STORAGE_UPGRADE_READ, self::STORAGE_UPGRADE_ADMIN_READ])]
    public function getPaymentTypeInfo(): StoragePaymentInfo
    {
        return $this->getPaymentType()->getInfo();
    }

    /**
     * @return int
     */
    public function getPriceCzk(): int
    {
        return $this->priceCzk;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return int
     */
    #[Groups([self::STORAGE_UPGRADE_READ])]
    public function getSizeInGB(): int
    {
        return (int) (ByteSizeConverter::toGB($this->size));
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return StorageCardPayment|null
     */
    public function getStorageCardPayment(): ?StorageCardPayment
    {
        return $this->storageCardPayment;
    }
}
