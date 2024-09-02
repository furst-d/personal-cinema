<?php

namespace App\DTO\Video;

use OpenApi\Attributes as OA;

class PublicVideoSharesResponse
{
    #[OA\Property(description: "Used storage in bytes")]
    public int $maxViews;

    #[OA\Property(
        description: "List of public shares",
        type: "array",
        items: new OA\Items(ref: "#/components/schemas/ShareVideoPublic")
    )]
    public array $shares;

    /**
     * @param int $maxViews
     * @param array $shares
     */
    public function __construct(int $maxViews, array $shares)
    {
        $this->maxViews = $maxViews;
        $this->shares = $shares;
    }
}
