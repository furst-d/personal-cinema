<?php

namespace App\Repository\Settings;

use App\Entity\Settings\Settings;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Settings>
 */
class SettingsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Settings::class);
    }

    /**
     * @return string
     */
    public function getMaxFileSize(): string
    {
        return $this->findOneBy(['key' => 'video_size_limit'])->getValue();
    }

    /**
     * @return string
     */
    public function getDefaultUserStorageLimit(): string
    {
        return $this->findOneBy(['key' => 'default_user_storage_limit'])->getValue();
    }
}
