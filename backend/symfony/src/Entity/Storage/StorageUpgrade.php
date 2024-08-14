<?php

namespace App\Entity\Storage;

use App\Entity\Account\Account;
use App\Helper\Storage\StoragePaymentType;
use App\Repository\Storage\StorageUpgradeRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StorageUpgradeRepository::class)]
class StorageUpgrade
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'storageUpgrades')]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\Column(type: Types::INTEGER)]
    private int $paymentType;

    #[ORM\Column(type: Types::INTEGER)]
    private int $priceCzk;

    #[ORM\Column(type: Types::BIGINT)]
    private int $size;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\OneToOne(mappedBy: 'storageUpgrade', cascade: ['persist', 'remove'])]
    private ?StorageCardPayment $storageCardPayment = null;

    /**
     * @param Account $account
     * @param int $priceCzk
     * @param int $size
     * @param StoragePaymentType $paymentType
     * @param string|null $cardSessionid
     */
    public function __construct(Account $account, int $priceCzk, int $size, StoragePaymentType $paymentType, ?string $cardSessionid = null)
    {
        $this->account = $account;
        $this->paymentType = $paymentType->value;
        $this->priceCzk = $priceCzk;
        $this->size = $size;
        $this->createdAt = new DateTimeImmutable();

        if ($cardSessionid) {
            $this->storageCardPayment = new StorageCardPayment($this, $cardSessionid);
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
