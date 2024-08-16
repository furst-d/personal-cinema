<?php

namespace App\Helper\Storage;

class ByteSizeConverter
{
    /**
     * @param int $bytes
     * @return float
     */
    public static function toGB(int $bytes): float
    {
        return $bytes / 1024 / 1024 / 1024;
    }
}
