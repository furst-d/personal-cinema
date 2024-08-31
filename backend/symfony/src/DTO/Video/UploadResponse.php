<?php

namespace App\DTO\Video;

use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Attributes as OA;

class UploadResponse
{
    #[OA\Property(description: "Nonce for the upload. Is used to prevent replay attacks.")]
    public string $nonce;

    #[Assert\NotBlank]
    #[OA\Property(description: "Custom parameters for the upload. Is used to store additional information about the upload that are sent back in the callback.")]
    public string $params;

    #[Assert\NotBlank]
    #[OA\Property(description: "ID of the project the video is uploaded to")]
    public string $projectId;

    #[Assert\NotBlank]
    #[OA\Property(description: "Unique signature for the upload. Is used to verify the authenticity of the upload.")]
    public string $signature;

    /**
     * @param string $nonce
     * @param string $params
     * @param string $projectId
     * @param string $signature
     */
    public function __construct(string $nonce, string $params, string $projectId, string $signature)
    {
        $this->nonce = $nonce;
        $this->params = $params;
        $this->projectId = $projectId;
        $this->signature = $signature;
    }
}
