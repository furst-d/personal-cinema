<?php

namespace App\Helper\Storage;

enum StoragePaymentType: int
{
    case CARD = 1;
    case FREE = 2;

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return match($this) {
            self::CARD => 'Platební karta',
            self::FREE => 'Zdarma',
        };
    }

    /**
     * @return StoragePaymentInfo
     */
    public function getInfo(): StoragePaymentInfo
    {
        return new StoragePaymentInfo($this->value, $this->name, $this->getLabel());
    }

    /**
     * @return array
     */
    public static function getAllInfo(): array
    {
        $info = [];
        foreach (self::cases() as $case) {
            $info[] = $case->getInfo();
        }

        return $info;
    }
}
