<?php

namespace App\Entity\Storage;

use App\Entity\Account\Account;
use App\Repository\Storage\StorageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StorageRepository::class)]
class Storage
{
    public const STORAGE_READ = 'storage:read';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([self::STORAGE_READ])]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'storage', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([self::STORAGE_READ])]
    private Account $account;

    #[ORM\Column(type: 'bigint')]
    #[Groups([self::STORAGE_READ])]
    private int $maxStorage;

    #[ORM\Column(type: 'bigint')]
    #[Groups([self::STORAGE_READ])]
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

    /**
     * @return float
     */
    #[Groups([self::STORAGE_READ])]
    public function getFillSize(): float
    {
        return $this->usedStorage / $this->maxStorage * 100;
    }
}
