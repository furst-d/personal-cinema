<?php

namespace App\Helper\Storage;

use App\Entity\Storage\StorageUpgrade;
use Symfony\Component\Serializer\Attribute\Groups;

class StoragePaymentInfo
{
    /**
     * @var int $id
     */
    #[Groups([StorageUpgrade::STORAGE_UPGRADE_READ, StorageUpgrade::STORAGE_UPGRADE_ADMIN_READ])]
    public int $id;

    /**
     * @var string $name
     */
    #[Groups([StorageUpgrade::STORAGE_UPGRADE_READ, StorageUpgrade::STORAGE_UPGRADE_ADMIN_READ])]
    public string $name;

    /**
     * @var string $label
     */
    #[Groups([StorageUpgrade::STORAGE_UPGRADE_READ, StorageUpgrade::STORAGE_UPGRADE_ADMIN_READ])]
    public string $label;

    /**
     * @param int $id
     * @param string $name
     * @param string $label
     */
    public function __construct(int $id, string $name, string $label)
    {
        $this->id = $id;
        $this->name = $name;
        $this->label = $label;
    }
}
