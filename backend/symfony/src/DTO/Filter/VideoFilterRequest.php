<?php

namespace App\DTO\Filter;
use OpenApi\Attributes as OA;

class VideoFilterRequest extends FilterRequest {
    #[OA\Property(description: 'Video name')]
    public string $name;

    #[OA\Property(description: 'Owner email')]
    public string $email;

    #[OA\Property(description: 'Video MD5 hash')]
    public string $md5;

    #[OA\Property(description: 'Video unique hash')]
    public string $hash;

    #[OA\Property(description: 'Video CDN ID')]
    public string $cdnId;
}
