<?php

namespace App\Helper\Storage;

use App\Entity\Account\Account;

class StoragePaymentMetadata
{
    /**
     * @var Account $account
     */
    private Account $account;

    /**
     * @var int $priceCzk
     */
    private int $priceCzk;

    /**
     * @var int $size
     */
    private int $size;

    /**
     * @var StoragePaymentType $type
     */
    private StoragePaymentType $type;

    /**
     * @var string|null $stripeSessionId
     */
    private ?string $stripeSessionId;

    /**
     * @param Account $account
     * @param int $priceCzk
     * @param int $size
     * @param StoragePaymentType $type
     * @param string|null $stripeSessionId
     */
    public function __construct(Account $account, int $priceCzk, int $size, StoragePaymentType $type, ?string $stripeSessionId = null)
    {
        $this->account = $account;
        $this->priceCzk = $priceCzk;
        $this->size = $size;
        $this->type = $type;
        $this->stripeSessionId = $stripeSessionId;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
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
     * @return StoragePaymentType
     */
    public function getType(): StoragePaymentType
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }
}
