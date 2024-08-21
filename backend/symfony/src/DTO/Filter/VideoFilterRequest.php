<?php

namespace App\DTO\Filter;

class VideoFilterRequest extends FilterRequest {
    public string $name;
    public string $email;
    public string $md5;
    public string $hash;
    public string $cdnId;
}
