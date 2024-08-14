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
     * @param Account $account
     * @param int $priceCzk
     * @param int $size
     */
    public function __construct(Account $account, int $priceCzk, int $size)
    {
        $this->account = $account;
        $this->priceCzk = $priceCzk;
        $this->size = $size;
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
}
