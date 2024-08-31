<?php

namespace App\DTO\Video;

use OpenApi\Attributes as OA;

class VideoDownloadLinkResponse
{
    #[OA\Property(description: "Download link for the video")]
    public string $downloadLink;

    /**
     * @param string $downloadLink
     */
    public function __construct(string $downloadLink)
    {
        $this->downloadLink = $downloadLink;
    }
}
