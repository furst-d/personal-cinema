<?php

namespace App\DTO\Admin\Setting;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class SettingRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    public string $key;

    #[Assert\NotBlank]
    public string $value;
}
