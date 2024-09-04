<?php

namespace App\DTO\Admin\Setting;

use App\DTO\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class SettingRequest extends AbstractRequest
{
    #[Assert\NotBlank]
    #[OA\Property(description: 'Setting key')]
    public string $key;

    #[Assert\NotBlank]
    #[OA\Property(description: 'Setting value')]
    public string $value;
}
