<?php

namespace App\Helper\Storage;

class StoragePaymentInfo
{
    /**
     * @var int $id
     */
    public int $id;

    /**
     * @var string $name
     */
    public string $name;

    /**
     * @var string $label
     */
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
