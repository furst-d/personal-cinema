<?php

namespace App\DTO\Admin\Setting;

use Symfony\Component\Validator\Constraints as Assert;

class SettingRequest
{
    #[Assert\NotBlank]
    public string $key;

    #[Assert\NotBlank]
    public string $value;
}
