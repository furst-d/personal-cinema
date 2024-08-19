<?php

namespace App\Service\Video;

use App\DTO\Video\QualityRequest;
use App\Entity\Video\Video;
use App\Exception\InternalException;
use App\Helper\Generator\UrlGenerator;
use App\Service\Cdn\CdnService;

class ManifestService
{
    /**
     * @var CdnService $cdnService
     */
    private CdnService $cdnService;

    /**
     * @var UrlGenerator $urlGenerator
     */
    private UrlGenerator $urlGenerator;

    /**
     * @param CdnService $cdnService
     * @param UrlGenerator $urlGenerator
     */
    public function __construct(CdnService $cdnService, UrlGenerator $urlGenerator)
    {
        $this->cdnService = $cdnService;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Video $video
     * @param QualityRequest $qualityRequest
     * @return string
     * @throws InternalException
     */
    public function getManifest(Video $video, QualityRequest $qualityRequest): string
    {
        if ($qualityRequest->quality) {
            return $this->getQualityManifestContent($video, $qualityRequest->quality);
        }

        return $this->getMainManifestContent($video);
    }

    /**
     * @param Video $video
     * @return string
     * @throws InternalException
     */
    private function getMainManifestContent(Video $video): string {
        $manifestContent = "#EXTM3U\n";

        foreach ($video->getConversions() as $conversion) {
            $resolution = $conversion->getResolution();
            $bandwidth = $conversion->getBandwidth();

            $manifestContent .= "#EXT-X-STREAM-INF:BANDWIDTH=$bandwidth,RESOLUTION=$resolution\n";
            $manifestContent .= $this->urlGenerator->generateManifest($video, $conversion) . "\n";
        }

        return $manifestContent;
    }

    /**
     * @param Video $video
     * @param int $quality
     * @return string
     * @throws InternalException
     */
    private function getQualityManifestContent(Video $video, int $quality): string
    {
        return $this->cdnService->getManifest($video, $quality);
    }
}
