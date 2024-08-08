<?php

namespace App\Entity\Storage;

use App\Entity\Account\Account;
use App\Repository\Storage\StorageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StorageRepository::class)]
class Storage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'storage', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ORM\Column(type: 'bigint')]
    private int $maxStorage;

    #[ORM\Column(type: 'bigint')]
    private int $usedStorage;

    /**
     * @param Account $account
     * @param int $maxStorage
     */
    public function __construct(Account $account, int $maxStorage)
    {
        $this->account = $account;
        $this->maxStorage = $maxStorage;
        $this->usedStorage = 0;
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return int
     */
    public function getMaxStorage(): int
    {
        return $this->maxStorage;
    }

    /**
     * @return int
     */
    public function getUsedStorage(): int
    {
        return $this->usedStorage;
    }

    /**
     * @param int $maxStorage
     * @return void
     */
    public function setMaxStorage(int $maxStorage): void
    {
        $this->maxStorage = $maxStorage;
    }

    /**
     * @param int $usedStorage
     * @return void
     */
    public function setUsedStorage(int $usedStorage): void
    {
        $this->usedStorage = $usedStorage;
    }
}
