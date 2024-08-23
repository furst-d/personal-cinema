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
     * @return array
     */
    public function getInfo(): array
    {
        return [
            'id' => $this->value,
            'name' => $this->name,
            'label' => $this->getLabel()
        ];
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
